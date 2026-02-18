<div class="accordion-item bg-white text-dark border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-light text-dark" type="button" data-bs-toggle="collapse"
            data-bs-target="#distanceCollapse" aria-expanded="true" aria-controls="distanceCollapse">
            <i class="fas fa-ruler me-2"></i>Calculate Distance
        </button>
    </h2>
    <div id="distanceCollapse" class="accordion-collapse collapse show" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="calculateDistanceForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Point 1</label>
                    <select class="form-select bg-white text-dark border-secondary" name="point1_id" required>
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
            <div id="distanceResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 