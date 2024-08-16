<?php
    
?>

<script src="/js/functions.js"></script>


<smart-docking-layout id="dockinglayout"></smart-docking-layout>

<script type="module">
Smart('#dockinglayout', class {
    get properties() {
        return {
            layout: [
                {
                    type: 'LayoutGroup',
                    orientation: 'horizontal',
                    size: '50%',
                    items: [
                        {
                            type: 'LayoutPanel',
                            id: 'tabPanel',
                            label: 'Stats',
                            size: '35%',
                            items: [
                                {
                                    label: 'Character',
                                    content: 'Character<br>stuff<br>here'
                                },
                                {
                                    label: 'Familiar',
                                    content: "Familiar\nStuff\nHere\n"
                                }
                            ]
                        },
                        {
                            type: 'LayoutPanel',
                            label: '',
                            items: [
                                {
                                    id: 'outputTab',
                                    label: 'Inventory',
                                    headerPosition: 'none',
                                    content: 'BOXES UPON BOXES'
                                },
                                {
                                    id: 'outputTab',
                                    label: 'Stash',
                                    headerPosition: 'none',
                                    content: 'b o x e s'
                                }
                            ]
                        }
                    ]
                },
                {
                    type: 'LayoutGroup',
                    orientation: 'vertical',
                    items: [
                        {
                            type: 'LayoutPanel',
                            id: 'tabPanel',
                            label: 'Character',
                            items: [
                                {
                                    label: 'TextBox Tab',
                                    content: 'Write more text here ...'
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    }
});
</script>