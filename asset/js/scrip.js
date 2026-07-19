const sheetApiUrl = "https://sheets.googleapis.com/v4/spreadsheets/1H_nvVejVAC9HuzvFQOiruK-Z39tx3Xzi94kFAC69kbM/values/Keturunan!B3:E?key=AIzaSyAkjLLGuoaJ0IkFQTSlxsLH2mhI1Rl6kVc";


let chart;

async function loadChartData() {
    try {
        let response = await fetch(sheetApiUrl);
        let dataFromSheet = await response.json();

        let dataWithoutFirst = dataFromSheet.values.slice(1)

        let itemsMap = {};
        dataWithoutFirst.forEach(function (row) {
            let id = row[0];
            itemsMap[id] = {
                id: id,
                Nama: row[1],
                Deskripsi: row[2] || "-",
                pid: (row[3] === '-' || !row[3]) ? null : row[3],
                Generasi: 1
            };
        });

        let cache = {};
        function getGenerasi(id) {
            if (cache[id] !== undefined) return cache[id];

            let item = itemsMap[id];
            if (!item || !item.pid || !itemsMap[item.pid]) {
                cache[id] = 1;
                return 1;
            }

            cache[id] = getGenerasi(item.pid) + 1;
            return cache[id];
        }

        let formattedNodes = Object.keys(itemsMap).map(function (id) {
            let item = itemsMap[id];
            item.Generasi = getGenerasi(id);
            return item;
        });

        const urlNodeId = new URLSearchParams(window.location.search).get('nodeId');

        const colors = [
            '#ff6467',
            '#00d492',
            '#e67f0a',
            '#ed6aff',
            '#7c86ff',
            '#a6a807',
            '#30b6c0',
        ];

        const strokes = [
            "",
            "10,40,4",
            "10,10",
            "20,25",
            "100",
        ];

        OrgChart.templates.ana.node = function (node, data, template, config) {

            const finalColor = (data.Generasi - 1) % colors.length;

            return `<rect 
            stroke-dasharray="${strokes[data.pid % 5]}"
             x="0" 
             y="0" 
             width="300" 
             height="80"
             fill="${colors[finalColor]}"
             stroke="#000"
             stroke-width="3"
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
                style="font-size:20px; font-weight: 600;"
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

        OrgChart.templates.myTemplate.field_2 = function (node, data, template, config) {
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

        chart = new OrgChart(document.getElementById("tree"), {
            lazyLoading: true,
            orientation: OrgChart.orientation.left,
            editForm: {
                titleBinding: "Nama",
                buttons: {
                    pdf: null,
                },
                edit: { text: "Edit" },
                save: { text: "Save" }
            },
            searchFields: ["Nama", "Deskripsi"],
            highlightOnHover: "parents",
            mouseScroll: OrgChart.action.zoom,
            layout: OrgChart.layout.normal,
            enableSearch: true,
            scaleInitial: 0.6,
            template: "myTemplate",
            // miniMap: true,
            buttons: {
                edit: {
                    icon: OrgChart.icon.edit(24, 24, '#fff'),
                    text: 'Edit',
                    hideIfEditMode: true,
                    hideIfDetailsMode: false
                },
                share: {
                    icon: OrgChart.icon.share(24, 24, '#fff'),
                    text: 'Share'
                },
                pdf: {
                    icon: OrgChart.icon.pdf(24, 24, '#fff'),
                    text: 'Save as PDF'
                },
                remove: {
                    icon: OrgChart.icon.remove(24, 24, '#fff'),
                    text: 'Remove',
                    hideIfDetailsMode: true
                }
            },
            generateElementsFromFields: false,
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
            nodes: formattedNodes
        });

        chart.center(1);

        chart.searchUI.on("add-item", function (sender, args) {
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

    } catch (error) {


        document.getElementById("tree").innerHTML = '<div style="padding: 20px;">Gagal Mendapatkan Data!</div>'
        console.error("Gagal mengambil data dari Google Sheets:", error);
    }
}

loadChartData();
