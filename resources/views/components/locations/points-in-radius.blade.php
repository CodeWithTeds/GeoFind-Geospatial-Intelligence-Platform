<div class="accordion-item bg-dark text-light border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-secondary text-white collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#radiusCollapse" aria-expanded="false" aria-controls="radiusCollapse">
            <i class="fas fa-hotel me-2"></i>Find Hotels in Radius
        </button>
    </h2>
    <div id="radiusCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="radiusForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Center Point</label>
                    <select name="point_id" class="form-select bg-dark text-light border-secondary" required>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"> {{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Radius (KM)</label>
                    <div class="input-group">
                        <input type="number" class="form-control bg-dark text-light border-secondary" 
                            name="radius" required min="0.1" step="0.1" value="5">
                        <span class="input-group-text bg-dark text-light border-secondary">km</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Find Hotels
                </button>
            </form>
            <div id="radiusResult" class="mt-3 p-3 border border-secondary rounded"></div>
            <div id="hotelMapContainer" class="mt-3" style="height: 500px; display: none;"></div>
        </div>
    </div>
</div> 