<div class="accordion-item bg-white text-dark border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-light text-dark collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#bearingCollapse" aria-expanded="false" aria-controls="bearingCollapse">
            <i class="fas fa-compass me-2"></i>Calculate Bearing
        </button>
    </h2>
    <div id="bearingCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="bearingForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Point 1</label>
                    <select name="point1_id" class="form-select bg-white text-dark border-secondary" required>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"> {{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Point 2</label>
                    <select class="form-select bg-white text-dark border-secondary" name="point2_id" required>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"> {{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calculator me-2"></i>Calculate
                </button>
            </form>
            <div id="bearingResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 