<div class="accordion-item bg-dark text-light border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-secondary text-white" type="button" data-bs-toggle="collapse"
            data-bs-target="#convexHullCollapse" aria-expanded="false" aria-controls="convexHullCollapse">
            <i class="fas fa-draw-polygon me-2"></i>Convex Hull Calculator
        </button>
    </h2>
    <div id="convexHullCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="convexHullForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Select at least 3 points</label>
                    <select class="form-select bg-dark text-light border-secondary" name="point_ids[]" multiple required>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"> {{ $location->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text text-light">Hold Ctrl/Cmd to select multiple points</div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calculator me-2"></i>Calculate Convex Hull
                </button>
            </form>
            <div id="convexHullResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 