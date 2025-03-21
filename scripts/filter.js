/**
 * Schedule Filtering System
 * Handles filtering of schedule cards by day, professor, and room
 */
document.addEventListener('DOMContentLoaded', function() {
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const scheduleSearchInput = document.getElementById('scheduleSearchInput');
    
    console.log('Filter system initializing...');
    console.log('Found filter checkboxes:', filterCheckboxes.length);
    console.log('Clear filters button found:', !!clearFiltersBtn);
    
    // Store active filters
    let activeFilters = {
        day: [],
        professor: [],
        room: []
    };
    
    // Function to update filters when checkboxes are clicked
    function updateFilters() {
        console.log('Updating filters...');
        
        // Reset active filters
        activeFilters = {
            day: [],
            professor: [],
            room: []
        };
        
        // Collect all checked filters
        filterCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const filterType = checkbox.getAttribute('data-filter-type');
                const filterValue = checkbox.value;
                activeFilters[filterType].push(filterValue);
                console.log(`Added filter: ${filterType} = ${filterValue}`);
            }
        });
        
        // Apply the filters
        applyFilters();
    }
    
    // Apply filters to schedule cards
    function applyFilters() {
        console.log('Applying filters:', activeFilters);
        
        const searchTerm = scheduleSearchInput ? scheduleSearchInput.value.toLowerCase() : '';
        
        // Get fresh reference to schedule cards
        const scheduleCards = document.querySelectorAll('#schedules .schedule-card-container');
        console.log('Found schedule cards:', scheduleCards.length);
        
        scheduleCards.forEach(card => {
            let matchesFilters = true;
            
            try {
                // Day filter
                if (activeFilters.day.length > 0) {
                    const dayElement = card.querySelector('.fa-calendar').parentNode;
                    const day = dayElement.textContent.trim();
                    console.log('Checking day:', day, 'against filters:', activeFilters.day);
                    if (!activeFilters.day.some(filterDay => day.includes(filterDay))) {
                        matchesFilters = false;
                    }
                }
                
                // Professor filter
                if (activeFilters.professor.length > 0 && matchesFilters) {
                    const professorElement = card.querySelector('small.text-muted');
                    const professor = professorElement.textContent.replace('Prof. ', '').trim();
                    console.log('Checking professor:', professor, 'against filters:', activeFilters.professor);
                    if (!activeFilters.professor.includes(professor)) {
                        matchesFilters = false;
                    }
                }
                
                // Room filter
                if (activeFilters.room.length > 0 && matchesFilters) {
                    const roomElement = card.querySelector('.fa-door-open').parentNode;
                    const room = roomElement.textContent.replace('Room ', '').trim();
                    console.log('Checking room:', room, 'against filters:', activeFilters.room);
                    if (!activeFilters.room.includes(room)) {
                        matchesFilters = false;
                    }
                }
                
                // Search term filter
                if (searchTerm && matchesFilters) {
                    const cardText = card.textContent.toLowerCase();
                    if (!cardText.includes(searchTerm)) {
                        matchesFilters = false;
                    }
                }
                
                // Show/hide the card based on the filter result
                card.style.display = matchesFilters ? '' : 'none';
                console.log('Card display:', card.style.display);
            } catch (error) {
                console.error('Error filtering card:', error);
                // If there's an error, show the card by default
                card.style.display = '';
            }
        });
    }
    
    // Add event listeners to checkboxes
    filterCheckboxes.forEach(checkbox => {
        console.log('Adding event listener to checkbox:', checkbox.id);
        checkbox.addEventListener('change', updateFilters);
    });
    
    // Clear all filters button
    if (clearFiltersBtn) {
        console.log('Adding event listener to clear filters button');
        clearFiltersBtn.addEventListener('click', function() {
            console.log('Clearing all filters...');
            
            // Uncheck all checkboxes
            filterCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset active filters
            activeFilters = {
                day: [],
                professor: [],
                room: []
            };
            
            // If search input exists, clear it
            if (scheduleSearchInput) {
                scheduleSearchInput.value = '';
            }
            
            // Show all cards - get fresh reference
            const scheduleCards = document.querySelectorAll('#schedules .schedule-card-container');
            scheduleCards.forEach(card => {
                card.style.display = '';
            });
            
            console.log('All filters cleared');
        });
    }
    
    // Connect search input to filter
    if (scheduleSearchInput) {
        console.log('Adding event listener to search input');
        scheduleSearchInput.addEventListener('keyup', applyFilters);
    }
    
    console.log('Filter system initialized');
});