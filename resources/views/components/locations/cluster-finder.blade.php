<div class="accordion-item bg-dark text-light border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-secondary text-white" type="button" data-bs-toggle="collapse"
            data-bs-target="#clusterCollapse" aria-expanded="false" aria-controls="clusterCollapse">
            <i class="fas fa-object-group me-2"></i>Location Clusters
        </button>
    </h2>
    <div id="clusterCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="clusterForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">North Boundary</label>
                    <input type="number" class="form-control bg-dark text-light border-secondary" 
                           name="north" step="any" required placeholder="Latitude (e.g. 40.7128)">
                </div>
                <div class="mb-3">
                    <label class="form-label">South Boundary</label>
                    <input type="number" class="form-control bg-dark text-light border-secondary" 
                           name="south" step="any" required placeholder="Latitude (e.g. 40.7000)">
                </div>
                <div class="mb-3">
                    <label class="form-label">East Boundary</label>
                    <input type="number" class="form-control bg-dark text-light border-secondary" 
                           name="east" step="any" required placeholder="Longitude (e.g. -73.9900)">
                </div>
                <div class="mb-3">
                    <label class="form-label">West Boundary</label>
                    <input type="number" class="form-control bg-dark text-light border-secondary" 
                           name="west" step="any" required placeholder="Longitude (e.g. -74.0060)">
                </div>
                <div class="mb-3">
                    <label class="form-label">Max Distance Between Points (km)</label>
                    <input type="number" class="form-control bg-dark text-light border-secondary" 
                           name="max_distance" min="0.1" max="100" value="5" step="0.1" required>
                    <div class="form-text text-light">Maximum distance between points to be considered in the same cluster</div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Find Clusters
                </button>
            </form>
            <div id="clusterResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 

