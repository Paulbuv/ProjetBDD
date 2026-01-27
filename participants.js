document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('participantsTable');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const headers = table.querySelectorAll('.sortable');
    const filters = table.querySelectorAll('.column-filter');
    
    let currentSort = { column: null, direction: null };

    // Fonction de tri
    function sortTable(columnIndex, dataType) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Déterminer la direction du tri
        let direction = 'asc';
        if (currentSort.column === columnIndex) {
            if (currentSort.direction === 'asc') {
                direction = 'desc';
            } else if (currentSort.direction === 'desc') {
                direction = null; // Reset
            }
        }

        // Mettre à jour l'état visuel des en-têtes
        headers.forEach(header => {
            header.classList.remove('active', 'asc', 'desc');
        });

        if (direction) {
            const activeHeader = table.querySelector(`th[data-column="${columnIndex}"]`);
            activeHeader.classList.add('active', direction);

            // Trier les lignes
            rows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();

                let comparison = 0;
                if (dataType === 'number') {
                    const aNum = parseFloat(aValue) || 0;
                    const bNum = parseFloat(bValue) || 0;
                    comparison = aNum - bNum;
                } else {
                    comparison = aValue.localeCompare(bValue, 'fr', { sensitivity: 'base' });
                }

                return direction === 'asc' ? comparison : -comparison;
            });

            // Réorganiser les lignes dans le DOM
            rows.forEach(row => tbody.appendChild(row));

            currentSort = { column: columnIndex, direction: direction };
        } else {
            // Reset à l'ordre original
            location.reload(); // Simple reload pour reset
        }
    }

    // Fonction de filtrage
    function filterTable() {
        const rows = tbody.querySelectorAll('tr');
        let visibleCount = 0;

        rows.forEach(row => {
            let shouldShow = true;

            filters.forEach(filter => {
                const columnIndex = parseInt(filter.dataset.column);
                const filterValue = filter.value.toLowerCase().trim();
                
                if (filterValue) {
                    const cellValue = row.cells[columnIndex].textContent.toLowerCase().trim();
                    if (!cellValue.includes(filterValue)) {
                        shouldShow = false;
                    }
                }
            });

            if (shouldShow) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        // Mettre à jour le compteur de résultats
        updateResultsCount(visibleCount, rows.length);
    }

    // Afficher le nombre de résultats
    function updateResultsCount(visible, total) {
        let counter = document.querySelector('.results-count');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'results-count';
            table.parentNode.insertBefore(counter, table);
        }

        if (visible < total) {
            counter.innerHTML = `Affichage de <strong>${visible}</strong> participant(s) sur <strong>${total}</strong>`;
            counter.style.display = 'block';
        } else {
            counter.style.display = 'none';
        }
    }

    // Attacher les événements de tri
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const columnIndex = parseInt(this.dataset.column);
            const dataType = this.dataset.type;
            sortTable(columnIndex, dataType);
        });
    });

    // Attacher les événements de filtrage
    filters.forEach(filter => {
        filter.addEventListener('input', filterTable);
    });

    // Empêcher le tri quand on clique dans les champs de filtre
    filters.forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
