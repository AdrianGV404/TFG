# core/views.py
import os
import logging
from django.shortcuts import render
from django.views.decorators.http import require_GET
from django.http import JsonResponse
from django.contrib.auth.models import User
from django.utils.text import slugify

from core.services.search_datasets import (
    search_by_title,
    search_by_keyword,
    search_by_spatial,
    search_by_category,
)
from core.services.sparql_service import (
    get_total_datasets,
    get_all_themes,
    get_dataset_counts_by_theme,
)

from core.services.ine_api_service import (
    is_ine_dataset,
    get_dataset_from_ine,
    extract_ine_idtable,
    INEApiError,
)

from core.utils.file_utils import (
    handle_dataset_file
)
from core.services.dataset_analyzer import (
    analyze_distribution_url
)
from core.services.distribution_parser import (
    parse_distribution_page
)

logger = logging.getLogger(__name__)

DATASETS_DIR = os.path.join("core", "data")


def home(request):
    return render(request, "core/home.html")


@require_GET
def test_conexion_bd_api(request):
    try:
        user_count = User.objects.count()
        return JsonResponse(
            {
                "success": True,
                "message": "¡Conexión con Django y PostgreSQL exitosa!",
                "user_count": user_count,
            },
            status=200,
        )
    except Exception as e:
        logger.exception("Error en conexión BD")
        return JsonResponse({"success": False, "message": str(e)}, status=500)


# --- Generic search view helper to avoid repetition ---
def _generic_search_view(
    request, required_params, fetch_function, filename_builder=None, extra_args_from_request=None
):
    """
    - required_params: list of parameter names that must exist in GET (e.g. ['title'] or ['spatial_type','spatial_value'])
    - fetch_function: function to call to obtain remote data (must accept args as tuple)
    - filename_builder: optional callable (params_dict, page_int) -> filename string
    - extra_args_from_request: optional callable (params_dict, page_int) -> tuple(fetch_args)
    """
    params = {}
    for p in required_params:
        params[p] = request.GET.get(p)

    # Validate required params
    missing = [p for p, v in params.items() if not v]
    if missing:
        return JsonResponse(
            {"success": False, "message": f"Faltan parámetros: {', '.join(missing)}"}, status=400
        )

    # Accept both 'page' and '_page' for backward compatibility; prefer 'page'
    page_raw = request.GET.get("page", request.GET.get("_page", "0"))
    try:
        page_int = int(page_raw)
        if page_int < 0:
            page_int = 0
    except ValueError:
        page_int = 0

    # Build filename for cached file
    if filename_builder:
        filename = filename_builder(params, page_int)
    else:
        # default: join values with underscore
        safe_vals = "_".join([str(v).replace(" ", "_") for v in params.values()])
        filename = f"{safe_vals}_{page_int}"

    # Build fetch args
    if extra_args_from_request:
        fetch_args = extra_args_from_request(params, page_int)
    else:
        # default: pass all params in order, then page
        fetch_args = tuple(list(params.values()) + [page_int])

    try:
        data, message, status = handle_dataset_file(
            directory=DATASETS_DIR, filename=filename, fetch_function=fetch_function, fetch_args=fetch_args
        )
    except Exception as e:
        logger.exception("Error interno al llamar handle_dataset_file")
        return JsonResponse({"success": False, "message": "Error interno del servidor"}, status=500)

    if status != 200 or not data:
        # message may already be meaningful from handle_dataset_file
        return JsonResponse({"success": False, "message": message}, status=status)

    result_obj = data.get("result", {}) if isinstance(data, dict) else {}
    # Prefer an explicit items_count returned by the fetch result if present; otherwise use len(items)
    items_count = None
    if isinstance(result_obj, dict) and "items_count" in result_obj:
        try:
            items_count = int(result_obj["items_count"])
        except Exception:
            items_count = None

    items = result_obj.get("items") if isinstance(result_obj, dict) else None
    computed_items_count = len(items) if isinstance(items, list) else 0
    if items_count is None:
        items_count = computed_items_count

    file_path = os.path.join(DATASETS_DIR, f"{slugify(filename)}.json")

    return JsonResponse(
        {
            "success": True,
            "message": message,
            "result": result_obj,
            "items_count": items_count,
            "file_path": file_path,
        },
        status=200,
    )

@require_GET
def search_by_title_view(request):
    return _generic_search_view(
        request,
        required_params=["title"],
        fetch_function=search_by_title,
        filename_builder=lambda params, page: f"{params['title']}_{page}",
        extra_args_from_request=lambda params, page: (params["title"], page),
    )


@require_GET
def search_by_keyword_view(request):
    return _generic_search_view(
        request,
        required_params=["keyword"],
        fetch_function=search_by_keyword,
        filename_builder=lambda params, page: f"{params['keyword']}_{page}",
        extra_args_from_request=lambda params, page: (params["keyword"], page),
    )


@require_GET
def search_by_spatial_view(request):
    return _generic_search_view(
        request,
        required_params=["spatial_type", "spatial_value"],
        fetch_function=search_by_spatial,
        filename_builder=lambda params, page: f"{params['spatial_type']}_{params['spatial_value']}_{page}",
        extra_args_from_request=lambda params, page: (params["spatial_type"], params["spatial_value"], page),
    )


@require_GET
def search_by_category_view(request):
    return _generic_search_view(
        request,
        required_params=["category"],
        fetch_function=search_by_category,
        filename_builder=lambda params, page: f"{params['category']}_{page}",
        extra_args_from_request=lambda params, page: (params["category"], page),
    )

@require_GET
def total_datasets_view(request):
    try:
        total = get_total_datasets()
        return JsonResponse({"success": True, "total_datasets": total})
    except Exception as e:
        logger.exception("Error obteniendo total datasets")
        return JsonResponse({"success": False, "message": str(e)}, status=500)


@require_GET
def all_themes_view(request):
    try:
        themes = get_all_themes()
        return JsonResponse({"success": True, "themes": themes}, safe=False)
    except Exception as e:
        logger.exception("Error obteniendo themes")
        return JsonResponse({"success": False, "message": str(e)}, status=500)


@require_GET
def dataset_counts_by_theme_view(request):
    try:
        data = get_dataset_counts_by_theme()
        return JsonResponse({"success": True, "themes": data})
    except Exception as e:
        logger.exception("Error dataset counts by theme")
        return JsonResponse({"success": False, "message": str(e)}, status=500)


@require_GET
def analyze_dataset_view(request):
    dataset_url = request.GET.get("url")
    fmt = (request.GET.get("format") or "").lower()
    rows_param = request.GET.get("rows")

    if not dataset_url:
        return JsonResponse({"success": False, "message": "Parámetro 'url' es obligatorio"}, status=400)

    supported_formats = [None, "", "json", "csv", "xml", "rdf+xml", "html", "pc-axis"]
    if fmt not in supported_formats:
        return JsonResponse({"success": False, "message": f"Formato '{fmt}' no soportado"}, status=415)

    try:
        max_rows = None if rows_param == "-1" else int(rows_param) if rows_param else 80
    except ValueError:
        max_rows = 80

    try:
        # Si es dataset del INE -> usar wstempus via our service
        if is_ine_dataset(dataset_url):
            try:
                data = get_dataset_from_ine(dataset_url, sample_rows=max_rows)
            except INEApiError as ie:
                logger.exception("Error INE API")
                return JsonResponse({"success": False, "message": str(ie)}, status=500)

            suggestion = {"type": "table", "title": f"Tabla INE {extract_ine_idtable(dataset_url)}"}
            return JsonResponse({
                "success": True,
                **data,
                "suggestions": [suggestion],  # 🔹 ya como array
                "format_detected": "ine-api"  # 🔹 para diferenciar
            }, status=200)

        # Flujo "normal" para CSV/JSON/XML/PC-AXIS ya implementado
        analysis_result = analyze_distribution_url(
            dataset_url,
            format_override=(fmt if fmt else None),
            sample_rows=max_rows if max_rows is not None else 999999,
        )
        return JsonResponse({"success": True, **analysis_result}, status=200)

    except Exception as e:
        logger.exception("Error analizando dataset")
        return JsonResponse({"success": False, "message": "Error interno del servidor"}, status=500)
    
@require_GET
def resolve_distribution_view(request):
    dist_url = request.GET.get("url")
    if not dist_url:
        return JsonResponse({"success": False, "message": "Falta parámetro 'url'"}, status=400)
    try:
        files = parse_distribution_page(dist_url)
        return JsonResponse({"success": True, "files": files})
    except Exception as e:
        return JsonResponse({"success": False, "message": str(e)}, status=500)

