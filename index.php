<?php


define('ABSPATH', dirname(__FILE__) . '/');

require_once './anggota.php';

$data = $data['values'];

?>

<!DOCTYPE html>
<html>

<head>
    <meta name="robots" content="noindex, nofollow">

    <title>Silsilah Keluarga Lōlkhōzitōlu Zebua</title>
    <meta name="title" content="Silsilah Keluarga Lōlkhōzitōlu Zebua">
    <meta name="description" content="Silsilah keluarga dari Lōlkhōzitōlu Zebua di Tumori">


    <link rel="stylesheet" href="./asset/css/style.css">
    <script src="./asset/lib/OrgChartJS/orgchart.js"></script>

</head>

<body>

    <h1>Silsilah Lōlkhōzitōlu Zebua</h1>

    <div id="tree"></div>

    <p>Dibuat oleh <a target="_blank" rel="nofollow noreferrer noopener " href="https://dionzebua">Dion Zebua</a>.</p>

    <script>
        const colors = [
            '#ff6467',
            '#ff8904',
            '#ffb900',
            '#00d492',
            '#ed6aff',
            '#7c86ff',
            '#364153',
        ];
        const urlNodeId = new URLSearchParams(window.location.search).get('nodeId');

        OrgChart.templates.ana.node = function(node, data, template, config) {

            const finalColor = (data.Generasi % colors.length) - 1
            return `<rect 
             x="0" 
             y="0" 
             width="300" 
             height="80"
             fill="${colors[finalColor]}"
             stroke="#aeaeae"
             stroke-width="1"
             rx="40"
             ry="40">
         </rect>`;
        }
        OrgChart.templates.myTemplate = Object.assign({}, OrgChart.templates.ana);
        OrgChart.templates.myTemplate.size = [300, 80];


        OrgChart.templates.myTemplate.ripple = {
            radius: 40,
            color: '#fff',
            duration: 2000,
            rect: {
                x: 0,
                y: 0,
                width: 300,
                height: 80,
                rx: 40,
                ry: 40
            }
        };

        OrgChart.templates.myTemplate.link = `
            <path stroke-linejoin="round" 
                  stroke="#000" 
                  stroke-width="2px" 
                  fill="none" 
                  d="{edge}" />
        `;

        OrgChart.templates.myTemplate.field_0 = `
            <text 
                class="field_0"
                data-width="170"
                style="font-size:18px; font-weight: 600;"
                fill="#ffffff"
                x="80"
                y="30"
                text-anchor="start">
                {val}
            </text>
        `;

        OrgChart.templates.myTemplate.field_1 = `
            <text 
                data-width="170"
                class="field_1"
                style="font-size:14px;"
                fill="#ffffff"
                x="80"
                y="55"
                text-anchor="start">
                {val}
            </text>
        `;
        OrgChart.templates.myTemplate.field_2 = function(node, data, template, config) {
            const genNumber = parseInt(data.Generasi.toString().replace(/\D/g, '')) || 1;
            const finalColor = (genNumber - 1) % colors.length;
            const targetColor = colors[finalColor];

            const val = data.Generasi;

            return `
                <circle
                    cx="40"
                    cy="40"
                    r="18"
                    fill="#ffffff"
                    stroke="#ffffff"
                    stroke-width="2">
                </circle>

                <text
                    class="field_2"
                    style="font-size:17px;font-weight:bold;"
                    fill="${targetColor}"
                    x="40"
                    y="46"
                    text-anchor="middle">
                    ${val}
                </text>
            `;
        };

        OrgChart.SEARCH_PLACEHOLDER = "Car nama / deskripsi";
        var chart = new OrgChart(document.getElementById("tree"), {
            editForm: {
                titleBinding: "Nama",
            },
            searchFields: ["Nama", "Deskripsi"],
            highlightOnHover: "parents",
            mouseScroll: OrgChart.action.zoom,
            // layout: OrgChart.layout.grid,
            enableSearch: true,
            scaleInitial: 0.5,
            template: "myTemplate",
            // miniMap: true,
            nodeBinding: {
                field_0: "Nama",
                field_1: "Deskripsi",
                field_2: "Generasi"
            },
            keyNavigation: {
                focusId: urlNodeId
            },
            controls: {
                fit: {
                    title: "Fit to Screen"
                },
                pdf_export: {
                    title: "Export to PDF"
                },
                zoom_in: {
                    title: "Zoom In"
                },
                zoom_out: {
                    textitle: "Zoom Out"
                },
            },
            orderBy: "id",
            nodes: <?= json_encode($data); ?>
        });
        chart.center(1);


        chart.searchUI.on("add-item", function(sender, args) {
            var data = sender.instance.get(args.nodeId);

            args.html = `<tr data-search-item-id="${data.id}">
                    <td class="search-result">
                        <div class="kiri">
                            <div>${data.Generasi}</div>
                        </div>
                        <div class="kanan">
                            <div class="nama">${data.Nama}</div>
                            <div class="deskripsi">${data.Deskripsi}</div>
                        </div>
                    </td>
                </tr>`;
        });
    </script>

</body>

</html>