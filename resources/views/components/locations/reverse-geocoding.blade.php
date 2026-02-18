<div class="accordion-item bg-white text-dark border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-light text-dark collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#geocodingCollapse" aria-expanded="false" aria-controls="geocodingCollapse">
            <i class="fas fa-map-marked-alt me-2"></i>Reverse Geocoding
        </button>
    </h2>
    <div id="geocodingCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="reverseGeocodingForm">
                @csrf
                <div class="mb-3">
                    <label for="location_id" class="form-label">Select Location</label>
                    <select name="location_id" id="location_id" class="form-select bg-white text-dark border-secondary" required>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}
                                ({{ $location->latitude }}, {{ $location->longitude }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search-location me-2"></i>Get Address
                </button>
            </form>
            <div id="geocodingResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 