import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin

def parse_distribution_page(url: str):
    resp = requests.get(url, timeout=15)
    resp.raise_for_status()
    soup = BeautifulSoup(resp.text, "html.parser")

    results = []
    for link in soup.select("a[href]"):
        href = urljoin(url, link["href"])  # asegura URL absoluta
        text = link.get_text(strip=True).lower()
        candidate = f"{href.lower()} {text}"

        if "csv" in candidate:
            fmt = "csv"
        elif "json" in candidate:
            fmt = "json"
        elif "xml" in candidate:
            fmt = "xml"
        elif "xls" in candidate or "xlsx" in candidate:
            fmt = "xls"
        elif "pc-axis" in candidate or "px" in candidate:
            fmt = "pc-axis"
        else:
            continue  # si no coincide, lo saltamos

        results.append({"format": fmt, "url": href})

    return results
