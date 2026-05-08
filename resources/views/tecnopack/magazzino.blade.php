<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazzino Tecnopack</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(29, minmax(40px, 1fr));
            gap: 1px;
            background-color: #ddd;
            padding: 1px;
            overflow-x: auto;
        }

        .grid-cell {
            width: 100%;
            height: 40px;
            background-color: white;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: opacity 0.2s;
            font-size: 12px;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 2px;
        }

        .grid-cell:hover {
            opacity: 0.8;
        }

        .color-picker {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .color-option {
            width: 32px;
            height: 32px;
            border: 2px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }

        .color-option.selected {
            border-color: #0d6efd;
        }

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .saving-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
<div class="container-fluid p-4">
    <h2 class="mb-4">Griglia Tecnopack</h2>
    <div class="grid-container" id="grid"></div>
</div>

<!-- Loading Indicator -->
<div id="loading">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Caricamento...</span>
    </div>
</div>

<!-- Saving Indicator -->
<div class="saving-indicator" id="savingIndicator">
    Salvataggio completato
</div>

<!-- Modal -->
<div class="modal fade" id="cellModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifica Cella</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="cellText" placeholder="Inserisci testo...">
                <div class="color-picker" id="colorPicker"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="saveButton">Salva</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
    // Configurazione
    const ROWS = 20;
    const COLS = 29;
    const COLORS = ['#ffffff', '#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#000000'];
    const API_URL = '/tecnopack/gridhandler'; // Modifica con il percorso corretto

    // Elementi DOM
    const grid = document.getElementById('grid');
    const modal = new bootstrap.Modal(document.getElementById('cellModal'));
    const cellTextInput = document.getElementById('cellText');
    const colorPicker = document.getElementById('colorPicker');
    const saveButton = document.getElementById('saveButton');
    const loadingElement = document.getElementById('loading');
    const savingIndicator = document.getElementById('savingIndicator');

    // Stato
    let gridState = Array(ROWS).fill().map(() =>
        Array(COLS).fill().map(() => ({ text: '', color: '#ffffff' }))
    );
    let selectedCell = { row: 0, col: 0 };
    let selectedColor = '#ffffff';

    // Funzioni di utility
    function showLoading() {
        loadingElement.style.display = 'flex';
    }

    function hideLoading() {
        loadingElement.style.display = 'none';
    }

    function showSavingIndicator() {
        savingIndicator.style.display = 'block';
        setTimeout(() => {
            savingIndicator.style.display = 'none';
        }, 2000);
    }

    // Caricamento dati dal database
    async function loadGridData() {
        showLoading();
        try {
            const response = await fetch(API_URL+"?action=load");
            if (!response.ok) throw new Error('Errore nel caricamento');

            const result = await response.json();

            console.log(result);
            if (result) {
                result.forEach(cell => {
                    gridState[cell.row_index][cell.col_index] = {
                        text: cell.cell_text || '',
                        color: cell.cell_color
                    };

                    console.log(gridState[cell.row_index][cell.col_index]);
                });
                createGrid();
            }
        } catch (error) {
            console.error('Errore nel caricamento dei dati:', error);
            alert('Errore nel caricamento dei dati. Riprova.');
        } finally {

            hideLoading();
        }
    }

    // Creazione della griglia
    function createGrid() {
        grid.innerHTML = '';
        for (let i = 0; i < ROWS; i++) {
            for (let j = 0; j < COLS; j++) {
                const cell = document.createElement('div');
                cell.className = 'grid-cell';
                cell.dataset.row = i;
                cell.dataset.col = j;
                cell.style.backgroundColor = gridState[i][j].color;
                cell.textContent = gridState[i][j].text;

                cell.addEventListener('click', () => openCellEditor(i, j));
                grid.appendChild(cell);
            }
        }
    }

    // Creazione del color picker
    function createColorPicker() {
        colorPicker.innerHTML = '';
        COLORS.forEach(color => {
            const colorOption = document.createElement('div');
            colorOption.className = 'color-option';
            colorOption.style.backgroundColor = color;
            colorOption.addEventListener('click', () => {
                document.querySelectorAll('.color-option').forEach(opt =>
                    opt.classList.remove('selected'));
                colorOption.classList.add('selected');
                selectedColor = color;
            });
            colorPicker.appendChild(colorOption);
        });
    }

    // Apertura dell'editor
    function openCellEditor(row, col) {
        selectedCell = { row, col };
        const cellState = gridState[row][col];

        cellTextInput.value = cellState.text;
        selectedColor = cellState.color;

        document.querySelectorAll('.color-option').forEach(opt => {
            opt.classList.toggle('selected', opt.style.backgroundColor === selectedColor);
        });

        modal.show();
    }

    // Salvataggio modifiche
    async function saveCellChanges() {
        const { row, col } = selectedCell;
        const newText = cellTextInput.value;
        const newColor = selectedColor;

        showLoading();
        try {
            const response = await fetch(API_URL+"?action=update", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    row_index: row,
                    col_index: col,
                    cell_text: newText,
                    cell_color: newColor
                })
            });

            if (!response.ok) throw new Error('Errore nel salvataggio');

            const result = await response.json();
            if (result.success) {
                // Aggiorna lo stato locale
                gridState[row][col] = {
                    text: newText,
                    color: newColor
                };

                const cell = grid.children[row * COLS + col];
                cell.textContent = newText;
                cell.style.backgroundColor = newColor;

                modal.hide();
                showSavingIndicator();
            }
        } catch (error) {
            console.error('Errore nel salvataggio:', error);
            alert('Errore nel salvataggio. Riprova.');
        } finally {
            hideLoading();
        }
    }

    // Inizializzazione
    createColorPicker();
    loadGridData();
    saveButton.addEventListener('click', saveCellChanges);
</script>
</body>
</html>