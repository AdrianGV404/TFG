// components/VisualizationModal.jsx
import React, { useState, useMemo, useEffect } from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, BarChart, Bar, PieChart, Pie, Cell, AreaChart, Area } from 'recharts';
import { Calendar, BarChart3, TrendingUp, Map, Settings, Filter, Eye, Info, X } from 'lucide-react';

const VisualizationModal = ({ isOpen, onClose, datasets, selectedDatasets }) => {
  const [chartType, setChartType] = useState('line');
  const [showSampleSize, setShowSampleSize] = useState(true);
  const [selectedCategories, setSelectedCategories] = useState({});
  const [selectedGender, setSelectedGender] = useState('total');
  const [showFilters, setShowFilters] = useState(false);
  const [processedDatasets, setProcessedDatasets] = useState([]);

  // Colores para consistencia visual
  const colors = ['#8884d8', '#82ca9d', '#ffc658', '#ff7300', '#00ff00', '#ff00ff'];

  // Procesar datasets seleccionados al abrir el modal
  useEffect(() => {
    if (isOpen && selectedDatasets.length > 0) {
      processSelectedDatasets();
    }
  }, [isOpen, selectedDatasets]);

  const processSelectedDatasets = async () => {
    try {
      // Llamada a tu backend para obtener y procesar los datos
      const response = await fetch('/api/process-datasets/', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          datasets: selectedDatasets,
          processing_options: {
            normalize_dates: true,
            clean_categories: true,
            extract_sample_info: true
          }
        })
      });

      const data = await response.json();
      setProcessedDatasets(data.processed_datasets);
      
      // Inicializar categorías disponibles
      if (data.available_categories) {
        const initialCategories = {};
        data.available_categories.forEach(cat => {
          initialCategories[cat.key] = cat.default_selected || false;
        });
        setSelectedCategories(initialCategories);
      }

    } catch (error) {
      console.error('Error procesando datasets:', error);
    }
  };

  // Procesar datos para visualización
  const processedData = useMemo(() => {
    if (processedDatasets.length === 0) return [];

    return processedDatasets.map(item => {
      const processedItem = {
        date: formatDate(item.date),
        shortDate: new Date(item.date).getFullYear().toString(),
        fullDate: item.date
      };

      // Agregar solo las categorías seleccionadas
      Object.keys(selectedCategories).forEach(category => {
        if (selectedCategories[category] && item.data[category]) {
          const genderKey = selectedGender === 'total' ? 'total' : 
                          selectedGender === 'hombres' ? 'male' : 'female';
          
          if (item.data[category][genderKey] !== undefined) {
            processedItem[item.data[category].display_name] = item.data[category][genderKey];
          }
        }
      });

      return processedItem;
    });
  }, [processedDatasets, selectedCategories, selectedGender]);

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'short'
    });
  };

  const handleCategoryChange = (category) => {
    setSelectedCategories(prev => ({
      ...prev,
      [category]: !prev[category]
    }));
  };

  // Datos para gráfico de torta (última fecha disponible)
  const pieData = useMemo(() => {
    if (processedData.length === 0) return [];
    const lastEntry = processedData[processedData.length - 1];
    return Object.keys(lastEntry)
      .filter(key => key !== 'date' && key !== 'shortDate' && key !== 'fullDate')
      .map((key, index) => ({
        name: key,
        value: lastEntry[key],
        fill: colors[index % colors.length]
      }));
  }, [processedData]);

  const renderChart = () => {
    const commonProps = {
      data: processedData,
      margin: { top: 5, right: 30, left: 20, bottom: 60 }
    };

    switch (chartType) {
      case 'line':
        return (
          <LineChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis 
              dataKey="shortDate" 
              angle={-45}
              textAnchor="end"
              height={80}
              interval={0}
            />
            <YAxis />
            <Tooltip 
              labelFormatter={(value) => `Año: ${value}`}
              formatter={(value, name) => [value?.toLocaleString('es-ES') || 0, name]}
            />
            <Legend />
            {Object.keys(selectedCategories)
              .filter(cat => selectedCategories[cat])
              .map((category, index) => {
                const displayName = processedDatasets[0]?.data[category]?.display_name || category;
                return (
                  <Line
                    key={category}
                    type="monotone"
                    dataKey={displayName}
                    stroke={colors[index]}
                    strokeWidth={2}
                    dot={{ r: 4 }}
                  />
                );
              })}
          </LineChart>
        );

      case 'bar':
        return (
          <BarChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis 
              dataKey="shortDate"
              angle={-45}
              textAnchor="end"
              height={80}
              interval={0}
            />
            <YAxis />
            <Tooltip 
              labelFormatter={(value) => `Año: ${value}`}
              formatter={(value, name) => [value?.toLocaleString('es-ES') || 0, name]}
            />
            <Legend />
            {Object.keys(selectedCategories)
              .filter(cat => selectedCategories[cat])
              .map((category, index) => {
                const displayName = processedDatasets[0]?.data[category]?.display_name || category;
                return (
                  <Bar
                    key={category}
                    dataKey={displayName}
                    fill={colors[index]}
                  />
                );
              })}
          </BarChart>
        );

      case 'area':
        return (
          <AreaChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis 
              dataKey="shortDate"
              angle={-45}
              textAnchor="end"
              height={80}
              interval={0}
            />
            <YAxis />
            <Tooltip 
              labelFormatter={(value) => `Año: ${value}`}
              formatter={(value, name) => [value?.toLocaleString('es-ES') || 0, name]}
            />
            <Legend />
            {Object.keys(selectedCategories)
              .filter(cat => selectedCategories[cat])
              .map((category, index) => {
                const displayName = processedDatasets[0]?.data[category]?.display_name || category;
                return (
                  <Area
                    key={category}
                    type="monotone"
                    dataKey={displayName}
                    stackId="1"
                    stroke={colors[index]}
                    fill={colors[index]}
                  />
                );
              })}
          </AreaChart>
        );

      case 'pie':
        return (
          <PieChart>
            <Pie
              data={pieData}
              cx="50%"
              cy="50%"
              labelLine={false}
              label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(1)}%`}
              outerRadius={80}
              fill="#8884d8"
              dataKey="value"
            >
              {pieData.map((entry, index) => (
                <Cell key={`cell-${index}`} fill={entry.fill} />
              ))}
            </Pie>
            <Tooltip formatter={(value) => value?.toLocaleString('es-ES') || 0} />
          </PieChart>
        );

      default:
        return null;
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-gray-900 text-white rounded-lg w-full max-w-6xl max-h-[90vh] overflow-hidden">
        {/* Header del modal */}
        <div className="flex items-center justify-between p-6 border-b border-gray-700">
          <h2 className="text-2xl font-bold">Análisis de Datos Seleccionados</h2>
          <div className="flex items-center gap-2">
            <button
              onClick={() => setShowFilters(!showFilters)}
              className="flex items-center gap-2 px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
            >
              <Settings size={20} />
              Configuración
            </button>
            <button
              onClick={onClose}
              className="p-2 hover:bg-gray-800 rounded-lg transition-colors"
            >
              <X size={24} />
            </button>
          </div>
        </div>

        {/* Contenido scrolleable */}
        <div className="overflow-y-auto max-h-[calc(90vh-80px)] p-6">
          {/* Información del dataset */}
          {showSampleSize && processedDatasets.length > 0 && (
            <div className="flex items-center gap-4 text-sm text-gray-300 bg-gray-800 p-3 rounded-lg mb-6">
              <div className="flex items-center gap-2">
                <Info size={16} />
                <span>Datasets: {selectedDatasets.length}</span>
              </div>
              <div className="flex items-center gap-2">
                <Calendar size={16} />
                <span>Registros: {processedDatasets.length}</span>
              </div>
              <div className="flex items-center gap-2">
                <Eye size={16} />
                <span>Vista: {selectedGender === 'total' ? 'Total' : selectedGender === 'hombres' ? 'Hombres' : 'Mujeres'}</span>
              </div>
            </div>
          )}

          {/* Panel de configuración */}
          {showFilters && (
            <div className="mb-6 p-4 bg-gray-800 rounded-lg">
              <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                <Filter size={20} />
                Filtros y Configuración
              </h3>
              
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Selección de categorías */}
                <div>
                  <h4 className="font-medium mb-3 text-blue-400">Categorías Disponibles</h4>
                  <div className="space-y-2 max-h-40 overflow-y-auto">
                    {Object.keys(selectedCategories).map(category => (
                      <label key={category} className="flex items-center gap-2 cursor-pointer">
                        <input
                          type="checkbox"
                          checked={selectedCategories[category]}
                          onChange={() => handleCategoryChange(category)}
                          className="w-4 h-4"
                        />
                        <span className="text-sm">
                          {processedDatasets[0]?.data[category]?.display_name || category}
                        </span>
                      </label>
                    ))}
                  </div>
                </div>

                {/* Selección de género */}
                <div>
                  <h4 className="font-medium mb-3 text-green-400">Segmentación</h4>
                  <div className="space-y-2">
                    {['total', 'hombres', 'mujeres'].map(gender => (
                      <label key={gender} className="flex items-center gap-2 cursor-pointer">
                        <input
                          type="radio"
                          name="gender"
                          value={gender}
                          checked={selectedGender === gender}
                          onChange={(e) => setSelectedGender(e.target.value)}
                          className="w-4 h-4"
                        />
                        <span className="capitalize">{gender}</span>
                      </label>
                    ))}
                  </div>
                </div>

                {/* Opciones de visualización */}
                <div>
                  <h4 className="font-medium mb-3 text-purple-400">Opciones</h4>
                  <label className="flex items-center gap-2 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={showSampleSize}
                      onChange={(e) => setShowSampleSize(e.target.checked)}
                      className="w-4 h-4"
                    />
                    <span>Mostrar información</span>
                  </label>
                </div>
              </div>
            </div>
          )}

          {/* Botones de tipo de gráfico */}
          <div className="mb-6">
            <h3 className="text-lg font-semibold mb-3">Tipo de Visualización</h3>
            <div className="flex flex-wrap gap-3">
              {[
                { type: 'line', label: 'Líneas', icon: TrendingUp },
                { type: 'bar', label: 'Barras', icon: BarChart3 },
                { type: 'area', label: 'Área', icon: TrendingUp },
                { type: 'pie', label: 'Circular', icon: Map }
              ].map(({ type, label, icon: Icon }) => (
                <button
                  key={type}
                  onClick={() => setChartType(type)}
                  className={`flex items-center gap-2 px-4 py-2 rounded-lg transition-colors ${
                    chartType === type
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
                  }`}
                >
                  <Icon size={18} />
                  {label}
                </button>
              ))}
            </div>
          </div>

          {/* Gráfico principal */}
          <div className="bg-gray-800 p-6 rounded-lg">
            <ResponsiveContainer width="100%" height={400}>
              {renderChart()}
            </ResponsiveContainer>
          </div>

          {/* Estadísticas resumidas */}
          {processedData.length > 0 && (
            <div className="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
              {Object.keys(selectedCategories)
                .filter(category => selectedCategories[category])
                .map(category => {
                  const displayName = processedDatasets[0]?.data[category]?.display_name || category;
                  const values = processedData.map(d => d[displayName]).filter(v => v !== undefined && v !== null);
                  
                  if (values.length === 0) return null;
                  
                  const total = values.reduce((sum, val) => sum + val, 0);
                  const avg = total / values.length;
                  const max = Math.max(...values);
                  
                  return (
                    <div key={category} className="bg-gray-800 p-4 rounded-lg">
                      <h4 className="font-medium text-blue-400 mb-2">{displayName}</h4>
                      <div className="space-y-1 text-sm">
                        <div>Total: {total.toLocaleString('es-ES')}</div>
                        <div>Promedio: {avg.toFixed(0).toLocaleString('es-ES')}</div>
                        <div>Máximo: {max.toLocaleString('es-ES')}</div>
                      </div>
                    </div>
                  );
                })}
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default VisualizationModal;