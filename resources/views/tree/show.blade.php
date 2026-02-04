@extends('layouts.app')

@section('title', 'Генеалогическое дерево')
@section('fullscreen', true)
@section('content')

    {{-- ===============================
    | 🌳 ФУЛСКРИН РЕЖИМ ДЛЯ ДЕРЕВА
    | применяется ТОЛЬКО на этой странице
    =============================== --}}
    <style>
        #tree {
            width: 100vw;
            height: calc(100vh - 120px); /* navbar + заголовок */
            overflow: hidden;
        }

        #tree svg {
            display: block;
            cursor: grab;
        }

        #tree svg:active {
            cursor: grabbing;
        }
    </style>

    <div class="tree-wrapper">
        <h1 class="mb-3 px-4 pt-3">Генеалогическое дерево</h1>
        <div id="tree"></div>
    </div>

    <script src="https://d3js.org/d3.v7.min.js"></script>

    <script>
        fetch("{{ route('tree.json', $person) }}")
            .then(r => r.json())
            .then(data => {

                const container = document.getElementById('tree');
                const width  = container.clientWidth;
                const height = container.clientHeight;

                const svg = d3.select("#tree")
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .attr("viewBox", [0, 0, width, height]);

                const g = svg.append("g");

                svg.call(
                    d3.zoom()
                        .scaleExtent([0.3, 2])
                        .on("zoom", e => g.attr("transform", e.transform))
                );

                const treeLayout = d3.tree().nodeSize([520, 300]);
                const root = d3.hierarchy(data.roots[0], d => d.children);
                treeLayout(root);
// ===============================
// 🔍 ZOOM + КОРРЕКТНАЯ ЦЕНТРОВКА
// ===============================
                const zoom = d3.zoom()
                    .scaleExtent([0.3, 2])
                    .on("zoom", (event) => {
                        g.attr("transform", event.transform);
                    });

                svg.call(zoom);

// 🎯 старт: центрируем корень дерева
// root.x / root.y = координаты корня после layout
                svg.call(
                    zoom.transform,
                    d3.zoomIdentity
                        .translate(
                            width / 2 - root.x,
                            120 - root.y
                        )
                        .scale(0.8)
                );

                /* ================== ЛИНИИ ================== */

                g.selectAll("path.link")
                    .data(root.links())
                    .enter()
                    .append("path")
                    .attr("fill", "none")
                    .attr("stroke", "#cbd5e1")
                    .attr("stroke-width", 2)
                    .attr("d", d => `
                        M${d.source.x},${d.source.y}
                        V${d.source.y + 80}
                        H${d.target.x}
                        V${d.target.y}
                    `);

                const node = g.selectAll("g.node")
                    .data(root.descendants())
                    .enter()
                    .append("g")
                    .attr("transform", d => `translate(${d.x},${d.y})`);

                /* =================================================
                 * 💍 БРАК
                 * ================================================= */

                const couples = node.filter(d => d.data.type === 'couple');

                couples.append("line")
                    .attr("y1", 30)
                    .attr("y2", 110)
                    .attr("stroke", "#111")
                    .attr("stroke-width", 2);

                couples.append("line")
                    .attr("x1", -260)
                    .attr("x2", 260)
                    .attr("y1", 0)
                    .attr("y2", 0)
                    .attr("stroke", "#111")
                    .attr("stroke-width", 2);

                couples.filter(d => d.data.years?.from)
                    .append("rect")
                    .attr("x", -160)
                    .attr("y", -140)
                    .attr("width", 320)
                    .attr("height", 36)
                    .attr("rx", 18)
                    .attr("fill", "#fff")
                    .attr("stroke", "#cbd5e1");

                couples.filter(d => d.data.years?.from)
                    .append("text")
                    .attr("y", -116)
                    .attr("text-anchor", "middle")
                    .attr("font-size", 13)
                    .text(d => {
                        const y = d.data.years;
                        let t = `${y.from} — ${y.to ?? 'н.в.'}`;
                        if (y.duration) t += ` · ${y.duration} лет`;
                        return t;
                    });

                /* 👩‍❤️‍👨 СУПРУГИ */
                couples.each(function(d) {
                    const group = d3.select(this);

                    const spouses = [];
                    if (d.data.husband) spouses.push(d.data.husband);
                    if (d.data.wife) spouses.push(d.data.wife);

                    spouses.forEach((p, i) => {
                        const x = i === 0 ? -260 : 260;
                        const gSpouse = group.append("g")
                            .attr("transform", `translate(${x},0)`);

                        const isDead = !!p.death_date;

                        gSpouse.append("rect")
                            .attr("x", -150)
                            .attr("y", -60)
                            .attr("width", 300)
                            .attr("height", 120)
                            .attr("rx", 26)
                            .attr("fill", "#fff")
                            .attr("stroke", p.gender === 'female' ? "#ec4899" : "#3b82f6")
                            .attr("stroke-width", 3)
                            .attr("stroke-dasharray", isDead ? "6,4" : null);

                        gSpouse.append("image")
                            .attr("href", p.photo)
                            .attr("x", -26)
                            .attr("y", -92)
                            .attr("width", 52)
                            .attr("height", 52)
                            .attr("clip-path", "circle(26px)");

                        gSpouse.append("text")
                            .attr("y", -4)
                            .attr("text-anchor", "middle")
                            .attr("font-size", 15)
                            .attr("font-weight", 700)
                            .text(p.name);

                        gSpouse.append("text")
                            .attr("y", 20)
                            .attr("text-anchor", "middle")
                            .attr("font-size", 12)
                            .attr("fill", "#555")
                            .text(() => {
                                if (p.birth_date && p.death_date) {
                                    const age = ageAtDeath(p.birth_date, p.death_date);
                                    return `⭐ ${year(p.birth_date)} — ${year(p.death_date)} · ${age} лет`;
                                }

                                if (p.birth_date) {
                                    const age = currentAge(p.birth_date);
                                    return `⭐ ${year(p.birth_date)} · ${age} лет`;
                                }
                                return '';
                            });

                        if (isDead) {
                            gSpouse.append("text")
                                .attr("y", 42)
                                .attr("text-anchor", "middle")
                                .attr("font-size", 16)
                                .text("🕯");
                        }

                        gSpouse.on("click", () => {
                            window.location.href = `/people/${p.id}`;
                        });
                    });
                });

                /* =================================================
                 * 👤 ЧЕЛОВЕК
                 * ================================================= */

                const persons = node.filter(d => d.data.type === 'person');

                persons.each(function(d) {
                    const gPerson = d3.select(this);
                    const isDead = !!d.data.death_date;

                    gPerson.append("rect")
                        .attr("x", -150)
                        .attr("y", -60)
                        .attr("width", 300)
                        .attr("height", 120)
                        .attr("rx", 26)
                        .attr("fill", "#eef4ff")
                        .attr("stroke", d.data.gender === "female" ? "#ec4899" : "#3b82f6")
                        .attr("stroke-width", 4)
                        .attr("stroke-dasharray", isDead ? "6,4" : null);

                    gPerson.append("image")
                        .attr("href", d.data.photo)
                        .attr("x", -26)
                        .attr("y", -92)
                        .attr("width", 52)
                        .attr("height", 52)
                        .attr("clip-path", "circle(26px)");

                    gPerson.append("text")
                        .attr("y", -4)
                        .attr("text-anchor", "middle")
                        .attr("font-size", 15)
                        .attr("font-weight", 700)
                        .text(d.data.name);

                    gPerson.append("text")
                        .attr("y", 20)
                        .attr("text-anchor", "middle")
                        .attr("font-size", 12)
                        .attr("fill", "#555")
                        .text(() => {
                            if (d.data.birth_date && d.data.death_date) {
                                const age = ageAtDeath(d.data.birth_date, d.data.death_date);
                                return `⭐ ${year(d.data.birth_date)} — ${year(d.data.death_date)} · ${age} лет`;
                            }

                            if (d.data.birth_date) {
                                const age = currentAge(d.data.birth_date);
                                return `⭐ ${year(d.data.birth_date)} · ${age} лет`;
                            }
                            return '';
                        });

                    if (isDead) {
                        gPerson.append("text")
                            .attr("y", 42)
                            .attr("text-anchor", "middle")
                            .attr("font-size", 16)
                            .text("🕯");
                    }

                    gPerson.on("click", () => {
                        window.location.href = `/people/${d.data.id}`;
                    });
                });

                function year(date) {
                    return new Date(date).getFullYear();
                }

                function currentAge(birth) {
                    const b = new Date(birth);
                    const now = new Date();
                    let age = now.getFullYear() - b.getFullYear();
                    const m = now.getMonth() - b.getMonth();
                    if (m < 0 || (m === 0 && now.getDate() < b.getDate())) age--;
                    return age;
                }

                function ageAtDeath(birth, death) {
                    const b = new Date(birth);
                    const d = new Date(death);
                    let age = d.getFullYear() - b.getFullYear();
                    const m = d.getMonth() - b.getMonth();
                    if (m < 0 || (m === 0 && d.getDate() < b.getDate())) age--;
                    return age;
                }
            });
    </script>

@endsection
