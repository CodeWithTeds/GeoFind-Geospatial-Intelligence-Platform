document.addEventListener('DOMContentLoaded', function() {
    new FinisherHeader({
        "count": 100,
        "size": {
            "min": 2,
            "max": 8,
            "pulse": 0
        },
        "speed": {
            "x": {
                "min": 0,
                "max": 0.4
            },
            "y": {
                "min": 0,
                "max": 0.6
            }
        },
        "colors": {
            "background": "#000000",
            "particles": [
                "#fbfcca",
                "#d7f3fe",
                "#ffd0a7"
            ]
        },
        "blending": "overlay",
        "opacity": {
            "center": 1,
            "edge": 0
        },
        "skew": 0,
        "shapes": [
            "c"
        ]
    });
});
