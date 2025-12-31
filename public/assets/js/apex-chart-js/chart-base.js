var chartInstances = {};

// Cài đặt chung cho ApexCharts
Apex.theme = {
    mode: "light",
    palette: "palette1",
    monochrome: {
        enabled: false,
        color: "#255aee",
        shadeTo: "light",
        shadeIntensity: 0.65,
    },
};

// Cài đặt toolbar mặc định cho export
const defaultToolbar = {
    show: true,
    tools: {
        download: true,
        selection: false,
        zoom: false,
        zoomin: false,
        zoomout: false,
        pan: false,
        reset: false,
    },
    export: {
        csv: {
            filename: "chart-data",
            columnDelimiter: ",",
            headerCategory: "category",
            headerValue: "value",
        },
        svg: {
            filename: "chart",
        },
        png: {
            filename: "chart",
        },
    },
};

const generateRandomColor = (opacity = 0.6) => {
    const r = Math.floor(Math.random() * 256);
    const g = Math.floor(Math.random() * 256);
    const b = Math.floor(Math.random() * 256);
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
};

// Hàm tạo mảng màu ngẫu nhiên không trùng lặp
const generateRandomColors = (count, opacity = 0.6) => {
    const colors = [
        "#0d6efd", // primary
        "#198754", // success
        "#ffc107", // warning
        "#dc3545", // danger
        "#6f42c1", // purple
        "#20c997", // teal
        "#fd7e14", // orange
        "#d63384", // pink
        "#6610f2", // indigo
        "#0dcaf0", // cyan
    ];
    const usedColors = new Set();

    while (colors.length < count) {
        const hue = Math.floor(Math.random() * 360);
        const saturation = 50 + Math.floor(Math.random() * 30); // 50-80%
        const lightness = 40 + Math.floor(Math.random() * 20); // 40-60%

        const colorKey = `${hue}-${saturation}-${lightness}`;

        if (!usedColors.has(colorKey)) {
            usedColors.add(colorKey);
            colors.push(
                `hsla(${hue}, ${saturation}%, ${lightness}%, ${opacity})`
            );
        }
    }

    return colors;
};

// Hàm tạo màu theo golden ratio để đảm bảo phân bố đều
const generateGoldenRatioColors = (count, opacity = 0.6) => {
    const colors = [];
    const goldenRatio = 0.618033988749895;
    let hue = Math.random(); // Bắt đầu với hue ngẫu nhiên

    for (let i = 0; i < count; i++) {
        hue = (hue + goldenRatio) % 1;
        const h = Math.floor(hue * 360);
        const s = 50 + (i % 3) * 15; // Saturation 50%, 65%, 80%
        const l = 45 + (i % 2) * 10; // Lightness 45%, 55%

        colors.push(`hsla(${h}, ${s}%, ${l}%, ${opacity})`);
    }

    return colors;
};

// Hàm tạo pie chart
const createPieChart = (
    elementId,
    labels,
    data,
    colors = null,
    chartName = "",
    height = "100%",
    width = "100%",
    tooltipUnit = ""
) => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Hủy chart cũ nếu tồn tại
    if (chartInstances[elementId]) {
        chartInstances[elementId].destroy();
        delete chartInstances[elementId];
    }

    // Tạo màu ngẫu nhiên nếu không truyền vào
    if (!colors || colors.length < labels.length) {
        colors = generateRandomColors(labels.length, 0.6);
    }

    const options = {
        fontFamily: "inherit",
        series: data,
        labels: labels,
        chart: {
            fontFamily: "inherit",
            type: "pie",
            height: height,
            width: width,
            toolbar: defaultToolbar,
            parentHeightOffset: 0,
        },
        title: {
            text: chartName,
            align: "center",
            style: {
                fontSize: "16px",
                fontWeight: "bold",
                color: "#333",
            },
        },
        colors: colors,
        legend: {
            position: "bottom",
            horizontalAlign: "center",
            floating: false,
            fontSize: "12px",
            itemMargin: {
                horizontal: 10,
                vertical: 5,
            },
            formatter: function (seriesName, opts) {
                // Truncate long names
                const maxLength = 15;
                if (seriesName.length > maxLength) {
                    return seriesName.substring(0, maxLength) + "...";
                }
                return seriesName;
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return val.toFixed(1) + "%";
            },
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return `${fmNumber(value)} (${tooltipUnit})`;
                },
            },
        },
        responsive: [
            {
                breakpoint: 768,
                options: {
                    chart: {
                        fontFamily: "inherit",
                        height: 300,
                    },
                    legend: {
                        position: "bottom",
                        fontSize: "11px",
                        itemMargin: {
                            horizontal: 5,
                            vertical: 2,
                        },
                    },
                },
            },
        ],
    };

    const chart = new ApexCharts(element, options);
    chart.render();
    chartInstances[elementId] = chart;

    return chart;
};

// Hàm tạo bar chart
const createBarChart = (
    elementId,
    labels,
    data,
    colors = null,
    label = "Số lượng",
    chartName = "",
    yAxisLabel = null,
    xAxisLabel = null,
    height = "100%",
    width = "100%",
    tooltipUnit = "",
    horizontal = false
) => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Hủy chart cũ nếu tồn tại
    if (chartInstances[elementId]) {
        chartInstances[elementId].destroy();
        delete chartInstances[elementId];
    }

    // Tạo màu ngẫu nhiên nếu không truyền vào
    if (!colors) colors = generateRandomColors(data.length);

    const options = {
        fontFamily: "inherit",
        series: [
            {
                name: label,
                data: data,
            },
        ],
        chart: {
            fontFamily: "inherit",
            type: "bar",
            height: height,
            width: width,
            toolbar: defaultToolbar,
            parentHeightOffset: 0,
        },
        plotOptions: {
            bar: {
                horizontal: horizontal,
                borderRadius: 4,
                distributed: true,
                dataLabels: {
                    position: "top",
                },
            },
        },
        dataLabels: {
            enabled: true,
            offsetX: 30,
            style: {
                fontSize: "12px",
                colors: ["#304758"],
            },
            formatter: function (val, opts) {
                const originalValue =
                    opts.w.config.series[opts.seriesIndex].data[
                        opts.dataPointIndex
                    ];
                return `${fmNumber(originalValue)} (${tooltipUnit})`;
            },
        },
        colors: colors,
        xaxis: {
            categories: labels,
            position: "bottom",
            title: {
                text: xAxisLabel || "",
            },
            labels: {
                rotate: -45,
                rotateAlways: false,
                style: {
                    fontSize: "11px",
                },
            },
        },
        yaxis: {
            title: {
                fontFamily: "inherit",
                text: yAxisLabel || "",
            },
        },
        title: {
            text: chartName,
            align: "center",
            style: {
                fontSize: "16px",
                fontWeight: "bold",
                color: "#333",
            },
        },
        grid: {
            padding: {
                left: 10,
                right: 10,
            },
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return `${fmNumber(value)} (${tooltipUnit})`;
                },
            },
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return `${fmNumber(value)} (${tooltipUnit})`;
                },
            },
        },
    };

    const chart = new ApexCharts(element, options);
    chart.render();
    chartInstances[elementId] = chart;

    return chart;
};

// Hàm tạo grouped bar chart
const createGroupedBarChart = (
    elementId,
    labels,
    datasets,
    colors = null,
    chartName = "",
    yAxisLabel = "Diện tích (ha)",
    xAxisLabel = "Độ tuổi cây"
) => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Hủy chart cũ nếu tồn tại
    if (chartInstances[elementId]) {
        chartInstances[elementId].destroy();
        delete chartInstances[elementId];
    }

    // Tạo màu ngẫu nhiên nếu không truyền vào
    if (!colors || colors.length < datasets.length) {
        colors = generateGoldenRatioColors(datasets.length, 0.6);
    }

    // Chuyển đổi datasets sang format của ApexCharts
    const series = datasets.map((dataset, index) => ({
        name: dataset.label,
        data: dataset.data,
    }));

    const options = {
        fontFamily: "inherit",
        series: series,
        chart: {
            fontFamily: "inherit",
            type: "bar",
            height: "100%",
            width: "100%",
            toolbar: defaultToolbar,
            parentHeightOffset: 0,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "55%",
                endingShape: "rounded",
                borderRadius: 4,
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ["transparent"],
        },
        xaxis: {
            categories: labels,
            title: {
                text: xAxisLabel,
            },
            labels: {
                style: {
                    fontSize: "11px",
                },
            },
        },
        yaxis: {
            title: {
                fontFamily: "inherit",
                text: yAxisLabel,
            },
        },
        colors: colors,
        fill: {
            opacity: 1,
        },
        legend: {
            position: "bottom",
            horizontalAlign: "center",
            floating: false,
            fontSize: "12px",
            itemMargin: {
                horizontal: 10,
                vertical: 5,
            },
            formatter: function (seriesName) {
                // Truncate long names
                const maxLength = 15;
                if (seriesName.length > maxLength) {
                    return seriesName.substring(0, maxLength) + "...";
                }
                return seriesName;
            },
        },
        title: {
            text: chartName,
            align: "center",
            style: {
                fontSize: "16px",
                fontWeight: "bold",
                color: "#333",
            },
        },
        grid: {
            padding: {
                left: 10,
                right: 10,
            },
        },
    };

    const chart = new ApexCharts(element, options);
    chart.render();
    chartInstances[elementId] = chart;

    return chart;
};

// Hàm tạo stacked bar chart với comparison
const createStackedBarWithComparisonChart = (
    elementId,
    labels,
    stackedDatasets,
    comparisonData,
    comparisonLabel = "",
    chartName = "",
    stackedColors = null,
    comparisonColor = null
) => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Hủy chart cũ nếu tồn tại
    if (chartInstances[elementId]) {
        chartInstances[elementId].destroy();
        delete chartInstances[elementId];
    }

    // Tạo màu cho datasets xếp chồng nếu không truyền vào
    if (!stackedColors || stackedColors.length < stackedDatasets.length) {
        stackedColors = generateGoldenRatioColors(stackedDatasets.length, 0.6);
    }

    // Tạo màu cho cột so sánh nếu không truyền vào
    if (!comparisonColor) {
        comparisonColor = "rgba(128, 128, 128, 0.6)";
    }

    // Chuyển đổi datasets sang format của ApexCharts
    const series = [];

    // Thêm các dataset xếp chồng
    stackedDatasets.forEach((dataset, index) => {
        series.push({
            name: dataset.label,
            data: dataset.data,
            group: "stacked",
        });
    });

    // Thêm dataset so sánh
    series.push({
        name: comparisonLabel,
        data: comparisonData,
        group: "comparison",
    });

    const allColors = [...stackedColors, comparisonColor];

    const options = {
        fontFamily: "inherit",
        series: series,
        chart: {
            fontFamily: "inherit",
            type: "bar",
            height: "100%",
            width: "100%",
            stacked: true,
            toolbar: defaultToolbar,
            parentHeightOffset: 0,
            events: {
                mounted: function (chartContext, config) {
                    // Force horizontal legend after mount
                    const legend =
                        chartContext.el.querySelector(".apexcharts-legend");
                    if (legend) {
                        legend.style.display = "flex";
                        legend.style.flexDirection = "row";
                        legend.style.flexWrap = "wrap";
                        legend.style.justifyContent = "center";
                        legend.style.gap = "5px 15px";
                    }
                },
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "70%",
                borderRadius: 4,
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 1,
            colors: ["transparent"],
        },
        xaxis: {
            categories: labels,
            labels: {
                rotate: -45,
                rotateAlways: false,
                style: {
                    fontSize: "11px",
                },
            },
        },
        yaxis: {
            title: {
                fontFamily: "inherit",
                text: "Diện tích (ha)",
            },
            labels: {
                formatter: function (val) {
                    return fmNumber(val) + " ha";
                },
            },
        },
        colors: allColors,
        legend: {
            show: true,
            position: "bottom",
            horizontalAlign: "center",
            floating: false,
            fontSize: "12px",
            offsetY: 5,
            itemMargin: {
                horizontal: 8,
                vertical: 4,
            },
            markers: {
                width: 12,
                height: 12,
                radius: 2,
            },
            formatter: function (seriesName) {
                return seriesName;
            },
            containerMargin: {
                top: 10,
            },
            customLegendItems: [],
            inverseOrder: false,
            width: undefined,
            height: undefined,
            onItemClick: {
                toggleDataSeries: true,
            },
            onItemHover: {
                highlightDataSeries: true,
            },
        },
        fill: {
            opacity: 1,
        },
        title: {
            text: chartName,
            align: "center",
            style: {
                fontSize: "16px",
                fontWeight: "bold",
                color: "#333",
            },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return fmNumber(val) + " ha";
                },
            },
        },
        grid: {
            padding: {
                left: 10,
                right: 10,
                bottom: 10,
            },
        },
    };

    const chart = new ApexCharts(element, options);
    chart.render();
    chartInstances[elementId] = chart;

    return chart;
};

// Hàm tạo line chart
const createLineChart = (
    elementId,
    labels,
    data,
    color = null,
    backgroundColor = null,
    label = "",
    chartName = ""
) => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Hủy chart cũ nếu tồn tại
    if (chartInstances[elementId]) {
        chartInstances[elementId].destroy();
        delete chartInstances[elementId];
    }

    // Tạo màu ngẫu nhiên nếu không truyền vào
    if (!color) {
        color = generateRandomColor(0.6);
    }

    const options = {
        fontFamily: "inherit",
        series: [
            {
                name: label,
                data: data,
            },
        ],
        chart: {
            fontFamily: "inherit",
            type: "line",
            height: 350,
            toolbar: defaultToolbar,
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
            width: 3,
        },
        xaxis: {
            categories: labels,
        },
        colors: [color],
        title: {
            text: chartName,
            align: "center",
            style: {
                fontSize: "16px",
                fontWeight: "bold",
                color: "#333",
            },
        },
        grid: {
            borderColor: "#e7e7e7",
            row: {
                colors: ["#f3f3f3", "transparent"],
                opacity: 0.5,
            },
        },
        markers: {
            size: 5,
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return fmNumber(value);
                },
            },
        },
    };

    const chart = new ApexCharts(element, options);
    chart.render();
    chartInstances[elementId] = chart;

    return chart;
};

// Helper function để hủy một chart cụ thể
const destroyChart = (id) => {
    if (chartInstances[id]) {
        chartInstances[id].destroy();
        delete chartInstances[id];
        return true;
    }
    return false;
};

// Helper function để hủy tất cả charts
const destroyAllCharts = () => {
    Object.keys(chartInstances).forEach((id) => {
        if (chartInstances[id]) {
            chartInstances[id].destroy();
        }
    });
    chartInstances = {};
};

// Hàm tạo table từ data
const createDataTable = (elementId, labels, seriesData, unit = "") => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Xóa nội dung cũ
    element.innerHTML = "";

    // Tính tổng
    const total = seriesData.reduce((sum, val) => sum + val, 0);

    // Tạo table HTML
    const tableHTML = `
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead class="table-primary sticky-top">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Tên</th>
                        <th class="text-end" style="width: 150px;">Diện tích (${unit})</th>
                        <th class="text-center" style="width: 100px;">Tỷ lệ (%)</th>
                    </tr>
                </thead>
                <tbody>
                    ${labels
                        .map((label, index) => {
                            const value = seriesData[index];
                            const percent =
                                total > 0
                                    ? ((value / total) * 100).toFixed(2)
                                    : 0;
                            return `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${label}</td>
                                <td class="text-end fw-bold">${fmNumber(
                                    value
                                )}</td>
                                <td class="text-center">
                                    ${createBadge(percent + "%", "info")}
                                </td>
                            </tr>
                        `;
                        })
                        .join("")}
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Tổng cộng:</td>
                        <td class="text-end fw-bold text-primary">${fmNumber(
                            total.toFixed(2)
                        )}</td>
                        <td class="text-center fw-bold">
                            ${createBadge("100%", "success")}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

    destroyChart(elementId);
    element.innerHTML = tableHTML;
};

/**
 * Tạo biểu đồ bar so sánh 2 cột
 */
function createComparisonBarChart(
    elementId,
    labels,
    series1Data,
    series2Data,
    series1Name = "Series 1",
    series2Name = "Series 2",
    title = "",
    subtitle = "",
    yaxisTitle = "",
    height = 350,
    width = "100%",
    unit = ""
) {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Xóa chart cũ nếu có
    element.innerHTML = "";

    const options = {
        series: [
            {
                name: series1Name,
                data: series1Data,
            },
            {
                name: series2Name,
                data: series2Data,
            },
        ],
        chart: {
            type: "bar",
            height: height,
            width: width,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false,
                },
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "55%",
                endingShape: "rounded",
                dataLabels: {
                    position: "top",
                },
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return fmNumber(val) + (unit ? " " + unit : "");
            },
            offsetY: -20,
            style: {
                fontSize: "12px",
                colors: ["#304758"],
            },
        },
        stroke: {
            show: true,
            width: 2,
            colors: ["transparent"],
        },
        xaxis: {
            categories: labels,
            labels: {
                style: {
                    fontSize: "12px",
                },
            },
        },
        yaxis: {
            title: {
                text: yaxisTitle,
            },
            labels: {
                formatter: function (val) {
                    return fmNumber(val) + (unit ? " " + unit : "");
                },
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return fmNumber(val) + (unit ? " " + unit : "");
                },
            },
        },
        legend: {
            position: "top",
            horizontalAlign: "center",
            offsetY: 0,
        },
        title: {
            text: title,
            align: "center",
        },
        subtitle: {
            text: subtitle,
            align: "center",
        },
        colors: generateRandomColors(2),
    };

    const chart = new ApexCharts(element, options);
    chart.render();
}

/**
 * Tạo biểu đồ cột chồng (Stacked Bar Chart)
 */
const createStackedBarChart = (
    elementId,
    categories,
    series,
    chartName = "",
    height = 350,
    width = "100%",
    unit = "",
    colors
) => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Hủy chart cũ nếu tồn tại
    if (chartInstances[elementId]) {
        chartInstances[elementId].destroy();
        delete chartInstances[elementId];
    }

    const options = {
        fontFamily: "inherit",
        series: series,
        chart: {
            fontFamily: "inherit",
            type: "bar",
            height: height,
            width: width,
            stacked: true,
            toolbar: defaultToolbar,
            parentHeightOffset: 0,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                columnWidth: "70%",
            },
        },
        dataLabels: {
            enabled: false,
        },
        colors: colors || generateRandomColors(series.length),
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    fontSize: "12px",
                    fontFamily: "inherit",
                },
            },
        },
        yaxis: {
            title: {
                text: unit ? `Số lượng (${unit})` : "Số lượng",
                style: {
                    fontFamily: "inherit",
                    fontSize: "12px",
                },
            },
            labels: {
                formatter: function (val) {
                    return fmNumber(Math.round(val));
                },
                style: {
                    fontSize: "11px",
                    fontFamily: "inherit",
                },
            },
        },
        legend: {
            position: "top",
            horizontalAlign: "center",
            fontSize: "12px",
            fontFamily: "inherit",
            itemMargin: {
                horizontal: 10,
                vertical: 5,
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return `${fmNumber(val)} ${unit}`;
                },
            },
        },
        title: {
            text: chartName,
            align: "center",
            style: {
                fontSize: "16px",
                fontWeight: "bold",
                color: "#333",
                fontFamily: "inherit",
            },
        },
        grid: {
            borderColor: "#e7e7e7",
            row: {
                colors: ["#f3f3f3", "transparent"],
                opacity: 0.5,
            },
        },
        responsive: [
            {
                breakpoint: 768,
                options: {
                    chart: {
                        height: 300,
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "80%",
                        },
                    },
                    legend: {
                        position: "bottom",
                        fontSize: "11px",
                    },
                },
            },
        ],
    };

    const chart = new ApexCharts(element, options);
    chart.render();
    chartInstances[elementId] = chart;

    return chart;
};

/**
 * Tạo biểu đồ kết hợp cột và đường (Mixed Chart)
 * Phù hợp cho việc hiển thị 2 loại dữ liệu khác nhau trên cùng 1 biểu đồ
 */
const createMixedChart = (
    elementId,
    categories,
    series1Data,
    series2Data,
    series1Name = "Series 1",
    series2Name = "Series 2",
    chartName = "",
    yAxis1Title = "",
    yAxis2Title = "",
    height = 350,
    width = "100%",
    unit1 = "",
    unit2 = "",
    colors
) => {
    const element = document.getElementById(elementId);
    if (!element) return;

    // Hủy chart cũ nếu tồn tại
    if (chartInstances[elementId]) {
        chartInstances[elementId].destroy();
        delete chartInstances[elementId];
    }

    const options = {
        fontFamily: "inherit",
        series: [
            {
                name: series1Name,
                type: "column",
                data: series1Data,
            },
            {
                name: series2Name,
                type: "line",
                data: series2Data,
            },
        ],
        chart: {
            fontFamily: "inherit",
            height: height,
            width: width,
            type: "line",
            toolbar: defaultToolbar,
            parentHeightOffset: 0,
        },
        stroke: {
            width: [0, 4],
            curve: "smooth",
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: "60%",
            },
        },
        colors: colors,
        dataLabels: {
            enabled: true,
            enabledOnSeries: [1],
            formatter: function (val) {
                return fmNumber(Math.round(val));
            },
            style: {
                fontSize: "11px",
                colors: ["#304758"],
            },
        },
        labels: categories,
        xaxis: {
            type: "category",
            labels: {
                style: {
                    fontSize: "11px",
                    fontFamily: "inherit",
                },
            },
        },
        yaxis: [
            {
                // Trục Y bên trái (Số lượt sửa)
                decimalsInFloat: 0, // QUAN TRỌNG: Không hiển thị số thập phân
                title: {
                    text: yAxis1Title,
                    style: {
                        fontFamily: "inherit",
                        fontSize: "12px",
                    },
                },
                labels: {
                    formatter: function (val) {
                        // Làm tròn thành số nguyên
                        return (
                            fmNumber(Math.round(val)) +
                            (unit1 ? " " + unit1 : "")
                        );
                    },
                    style: {
                        fontSize: "11px",
                        fontFamily: "inherit",
                    },
                },
            },
            {
                // Trục Y bên phải (Chi phí)
                opposite: true,
                decimalsInFloat: 0, // QUAN TRỌNG: Không hiển thị số thập phân
                title: {
                    text: yAxis2Title,
                    style: {
                        fontFamily: "inherit",
                        fontSize: "12px",
                    },
                },
                labels: {
                    formatter: function (val) {
                        // Làm tròn thành số nguyên
                        return (
                            fmNumber(Math.round(val)) +
                            (unit2 ? " " + unit2 : "")
                        );
                    },
                    style: {
                        fontSize: "11px",
                        fontFamily: "inherit",
                    },
                },
            },
        ],
        legend: {
            position: "top",
            horizontalAlign: "center",
            fontSize: "12px",
            fontFamily: "inherit",
            itemMargin: {
                horizontal: 10,
                vertical: 5,
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: [
                {
                    formatter: function (val) {
                        return (
                            fmNumber(Math.round(val)) +
                            (unit1 ? " " + unit1 : "")
                        );
                    },
                },
                {
                    formatter: function (val) {
                        return (
                            fmNumber(Math.round(val)) +
                            (unit2 ? " " + unit2 : "")
                        );
                    },
                },
            ],
        },
        title: {
            text: chartName,
            align: "center",
            style: {
                fontSize: "16px",
                fontWeight: "bold",
                color: "#333",
                fontFamily: "inherit",
            },
        },
        grid: {
            borderColor: "#e7e7e7",
            row: {
                colors: ["#f3f3f3", "transparent"],
                opacity: 0.5,
            },
        },
        responsive: [
            {
                breakpoint: 768,
                options: {
                    chart: {
                        height: 300,
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "80%",
                        },
                    },
                    legend: {
                        position: "bottom",
                        fontSize: "11px",
                    },
                    dataLabels: {
                        style: {
                            fontSize: "10px",
                        },
                    },
                },
            },
        ],
    };

    const chart = new ApexCharts(element, options);
    chart.render();
    chartInstances[elementId] = chart;

    return chart;
};
