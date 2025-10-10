<div class="accordion-item bg-dark text-light border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-secondary text-white" type="button" data-bs-toggle="collapse"
            data-bs-target="#heatmapCollapse" aria-expanded="false" aria-controls="heatmapCollapse">
            <i class="fas fa-fire me-2"></i>Location Heatmap
        </button>
    </h2>
    <div id="heatmapCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="heatmapForm">
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
                    <label class="form-label">Grid Size</label>
                    <input type="number" class="form-control bg-dark text-light border-secondary" 
                           name="grid_size" min="2" max="50" value="10" required>
                    <div class="form-text text-light">Resolution of the heatmap (2-50)</div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-fire me-2"></i>Generate Heatmap
                </button>
            </form>
            <div id="heatmapResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 