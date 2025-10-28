<div class="max-w-3xl w-full mx-auto">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border-2 border-red-200">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-red-600 mb-2">Subir Archivo</h1>
            <p class="text-gray-600">Selecciona un archivo</p>
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-left">
                <p class="text-sm text-gray-700 mb-1">
                    <strong class="text-blue-600">ℹ️ Detección de duplicados:</strong>
                </p>
                <p class="text-xs text-gray-600">
                    Se comparará la <strong>primera columna</strong> (ID principal) para identificar registros duplicados. Las filas duplicadas se marcarán en amarillo.
                </p>
            </div>
        </div>

        <form class="space-y-6">
            <div>
                <label for="archivo" class="block text-sm font-medium text-gray-700 mb-2">
                    Archivo CSV
                </label>
                <input
                    type="file"
                    id="archivo"
                    name="archivo"
                    accept=".csv"
                    class="w-full px-4 py-3 border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-600 file:text-white hover:file:bg-red-700 file:cursor-pointer"
                >
            </div>

            <!-- Área de previsualización -->
            <div id="preview-area" class="hidden">
                <div class="border-2 border-red-300 rounded-lg p-4 bg-white">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-red-600">Vista Previa</h3>
                        <div id="duplicate-badge" class="hidden">
                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                ⚠️ Duplicados detectados
                            </span>
                        </div>
                    </div>
                    <div id="table-container" class="overflow-x-auto max-h-96 overflow-y-auto">
                        <!-- La tabla se insertará aquí -->
                    </div>
                    <div id="duplicate-warning" class="hidden mt-3 p-3 bg-yellow-100 border border-yellow-400 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <strong>⚠️ Advertencia:</strong> Se han detectado <span id="duplicate-count" class="font-bold">0</span> registro(s) duplicado(s). Las filas duplicadas están marcadas en amarillo.
                        </p>
                    </div>
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
                Subir
            </button>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('archivo');

                if (fileInput) {
                    fileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        const previewArea = document.getElementById('preview-area');
                        const tableContainer = document.getElementById('table-container');

                        if (file && (file.type === 'text/csv' || file.name.endsWith('.csv'))) {
                            const reader = new FileReader();

                            reader.onload = function(event) {
                                const csv = event.target.result;
                                const lines = csv.split('\n');
                                const rows = [];

                                // Parsear el CSV (soporta comas y comillas)
                                lines.forEach(line => {
                                    if (line.trim()) {
                                        const row = [];
                                        let current = '';
                                        let inQuotes = false;

                                        for (let i = 0; i < line.length; i++) {
                                            const char = line[i];

                                            if (char === '"') {
                                                inQuotes = !inQuotes;
                                            } else if (char === ',' && !inQuotes) {
                                                row.push(current.trim());
                                                current = '';
                                            } else {
                                                current += char;
                                            }
                                        }
                                        row.push(current.trim());
                                        rows.push(row);
                                    }
                                });

                                // Detectar duplicados basados en la primera columna
                                const firstColumnValues = {};
                                const duplicateRowIndices = new Set();
                                let duplicateCount = 0;

                                // Obtener los valores de la primera columna (excluyendo encabezado)
                                for (let i = 1; i < rows.length; i++) {
                                    const firstCell = rows[i][0];

                                    if (firstColumnValues[firstCell]) {
                                        // Es duplicado
                                        duplicateRowIndices.add(i);
                                        duplicateRowIndices.add(firstColumnValues[firstCell]);
                                        duplicateCount++;
                                    } else {
                                        firstColumnValues[firstCell] = i;
                                    }
                                }

                                // Mostrar advertencia si hay duplicados
                                const duplicateBadge = document.getElementById('duplicate-badge');
                                const duplicateWarning = document.getElementById('duplicate-warning');
                                const duplicateCountSpan = document.getElementById('duplicate-count');

                                if (duplicateCount > 0) {
                                    duplicateBadge.classList.remove('hidden');
                                    duplicateWarning.classList.remove('hidden');
                                    duplicateCountSpan.textContent = duplicateCount * 2; // Se multiplica por 2 porque cada duplicado tiene 2 filas (original + duplicado)
                                } else {
                                    duplicateBadge.classList.add('hidden');
                                    duplicateWarning.classList.add('hidden');
                                }

                                // Crear tabla HTML
                                let tableHTML = '<table class="min-w-full border-collapse border border-red-200">';

                                rows.forEach((row, index) => {
                                    const isDuplicate = duplicateRowIndices.has(index);
                                    const rowClass = isDuplicate ? 'bg-yellow-200' : (index % 2 === 0 ? 'bg-white' : 'bg-red-50');

                                    tableHTML += `<tr class="${isDuplicate ? 'border-2 border-yellow-500' : ''}">`;

                                    row.forEach(cell => {
                                        if (index === 0) {
                                            // Encabezados
                                            tableHTML += `<th class="bg-red-600 text-white px-4 py-2 border border-red-500 text-left font-semibold">${cell || '&nbsp;'}</th>`;
                                        } else {
                                            // Datos
                                            if (isDuplicate) {
                                                tableHTML += `<td class="bg-yellow-200 px-4 py-2 border border-yellow-400 text-gray-700 font-medium">${cell || '&nbsp;'}</td>`;
                                            } else {
                                                tableHTML += `<td class="${rowClass} px-4 py-2 border border-red-200 text-gray-700">${cell || '&nbsp;'}</td>`;
                                            }
                                        }
                                    });

                                    tableHTML += '</tr>';
                                });

                                tableHTML += '</table>';
                                tableContainer.innerHTML = tableHTML;
                                previewArea.classList.remove('hidden');
                            };

                            reader.readAsText(file);
                        } else {
                            alert('Por favor, selecciona un archivo CSV válido.');
                        }
                    });
                }
            });
        </script>

        <div class="mt-6 text-center">
            <a href="/" class="text-red-600 hover:text-red-700 font-medium text-sm">
                ← Volver al inicio
            </a>
        </div>
    </div>
</div>
