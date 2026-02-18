<div class="accordion-item bg-white text-dark border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-light text-dark collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#triangleCollapse" aria-expanded="false" aria-controls="triangleCollapse">
            <i class="fas fa-draw-polygon me-2"></i>Calculate Triangle Area
        </button>
    </h2>
    <div id="triangleCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="triangleAreaForm">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Point 1</label>
                        <select class="form-select bg-white text-dark border-secondary" name="point1_id" required>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"> {{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Point 2</label>
                        <select class="form-select bg-white text-dark border-secondary" name="point2_id" required>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"> {{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Point 3</label>
                        <select class="form-select bg-white text-dark border-secondary" name="point3_id" required>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"> {{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calculator me-2"></i>Calculate
                </button>
            </form>
            <div id="triangleAreaResult" class="mt-3 p-3 border border-secondary rounded"></div>
        </div>
    </div>
</div> 