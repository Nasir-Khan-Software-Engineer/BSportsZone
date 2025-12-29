WinPos.CommonChart = (function () {
    var createPieChart = function (canvasId, labels, backgroundColor, datasets, formatType = 'currency') {
        var ctx = document.getElementById(canvasId).getContext("2d");

        if (window[canvasId + 'Instance']) {
            window[canvasId + 'Instance'].destroy();
        }
        window[canvasId + 'Instance'] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: datasets,
                    backgroundColor: backgroundColor
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top'
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var value = dataset.data[tooltipItem.index];
                            
                            var formatted;
                            if (formatType === 'currency') {
                                formatted = Number(value).toLocaleString('en-BD', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return data.labels[tooltipItem.index] + ": ৳" + formatted;
                            } else if (formatType === 'number') {
                                formatted = Number(value).toLocaleString('en-BD', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                                return data.labels[tooltipItem.index] + ": " + formatted;
                            } else {
                                // Default to currency for backward compatibility
                                formatted = Number(value).toLocaleString('en-BD', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return data.labels[tooltipItem.index] + ": ৳" + formatted;
                            }
                        }
                    }
                }
            }
        });
    }

    return {
        pieChart: createPieChart
    }
})();
