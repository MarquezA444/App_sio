<div class="max-w-3xl w-full mx-auto">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border-2 border-red-200">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-red-600 mb-2">Subir Archivo</h1>
            <p class="text-gray-600">Selecciona un archivo</p>
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
                    <h3 class="text-lg font-semibold text-red-600 mb-3">Vista Previa</h3>
                    <div id="table-container" class="overflow-x-auto max-h-96 overflow-y-auto">
                        <!-- La tabla se insertará aquí -->
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

                                // Crear tabla HTML
                                let tableHTML = '<table class="min-w-full border-collapse border border-red-200">';

                                rows.forEach((row, index) => {
                                    tableHTML += '<tr>';

                                    row.forEach(cell => {
                                        if (index === 0) {
                                            // Encabezados
                                            tableHTML += `<th class="bg-red-600 text-white px-4 py-2 border border-red-500 text-left font-semibold">${cell || '&nbsp;'}</th>`;
                                        } else {
                                            // Datos
                                            const bgClass = index % 2 === 0 ? 'bg-white' : 'bg-red-50';
                                            tableHTML += `<td class="${bgClass} px-4 py-2 border border-red-200 text-gray-700">${cell || '&nbsp;'}</td>`;
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
