
# Usage

``` 
<div style="width: 1000px; height: 800px;" id="tree"></div>

<script type="module">
    import OrgChart from './orgchart.esm.js';

    const chart = new OrgChart(document.getElementById("tree"), {
        nodeBinding: { field_0: "name" }
    });

    chart.load([
        { id: 1, name: "Denny Curtis" },
        { id: 2, pid: 1, name: "Ashley Barnett" },
        { id: 3, pid: 1, name: "Caden Ellison" }
    ]);

</script>
```

