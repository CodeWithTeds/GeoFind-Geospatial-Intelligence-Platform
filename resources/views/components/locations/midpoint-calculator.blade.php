<div class="accordion-item bg-dark text-light border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-secondary text-white collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#midpointCollapse" aria-expanded="false" aria-controls="midpointCollapse">
            <i class="fas fa-dot-circle me-2"></i>Calculate Midpoint
        </button>
    </h2>
    <div id="midpointCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="midpointForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Point 1</label>
                    <select class="form-select bg-dark text-light border-secondary" name="point1_id" required>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"> {{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Point 2</label>
                    <select class="form-select bg-dark text-light border-secondary" name="point2_id" required>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"> {{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calculator me-2"></i>Calculate
                </button>
            </form>
            <div id="midpointResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 