const dropArea = document.querySelector(".drop-area");
const dragText = dropArea.querySelector("p");
const button = dropArea.querySelector("button");
const input = dropArea.querySelector("#input-file");
let files;

button.addEventListener("click", (e) => {
    input.click();
});

input.addEventListener("change", (e) =>{
    files = input.files;
    dropArea.classList.add("active");
    showFile(files);
    dropArea.classList.remove("active");
});

dropArea.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropArea.classList.add("active");
    dragText.textContent = "Suelta para subir tus archivos";
});

dropArea.addEventListener("dragleave", (e) => {
    e.preventDefault();
    dropArea.classList.remove("active");
    dragText.textContent = "Arrastra tus archivos a subir aquí";

});

dropArea.addEventListener("drop", (e) => {
    e.preventDefault();
    files = e.dataTransfer.files;
    showFile(files);
    dropArea.classList.remove("active");
    dragText.textContent = "Arrastra tus archivos a subir aquí";

});



function showFile(files){
    if(files.length == undefined){
        processFile(files);
    }else{
        for(const file of files){
            processFile(file);
        }
    }
}

function processFile(file) {
    const fileExtension = file.name.split('.').pop().toLowerCase();
    const validExtensions = ['xlsx', 'xls'];
    
    if(validExtensions.includes(fileExtension)){
        //archivo válido
        const fileReader = new FileReader();
        const id = `file-${Math.random().toString(32).substring(7)}`;

        fileReader.addEventListener("load", (e) => {
            const filePreview = `
                <div id="${id}" class="file-container">
                    <div class="status">
                        <span>${file.name}</span>
                        <span class="status-text">
                            Cargando...
                        </span>
                    </div>
                </div>
            `;
            const html = document.querySelector('#preview').innerHTML;
            document.querySelector('#preview').innerHTML = filePreview + html;
        });

        fileReader.readAsDataURL(file);
        uploadFile(file, id);

    }else{
        //No válido
        alert("No es un archivo válido. Asegúrate de subir solamente archivos con extensión .xls y .xlsx");
    }
}

let filesToUpload = [];

function processFile(file) {
    const fileExtension = file.name.split('.').pop().toLowerCase();
    const validExtensions = ['xlsx', 'xls'];

    if (validExtensions.includes(fileExtension)) {
        //archivo válido
        const fileReader = new FileReader();
        const id = `file-${Math.random().toString(32).substring(7)}`;

        fileReader.addEventListener("load", (e) => {
            const filePreview = `
                <div id="${id}" class="file-container">
                    <div class="status">
                        <span>${file.name}</span>
                        <span class="status-text">
                            Listo para subir
                        </span>
                    </div>
                </div>
            `;
            const html = document.querySelector('#preview').innerHTML;
            document.querySelector('#preview').innerHTML = filePreview + html;
        });

        fileReader.readAsDataURL(file);
        filesToUpload.push({ file, id });
    } else {
        //No válido
        alert("No es un archivo válido. Asegúrate de subir solamente archivos con extensión .xls y .xlsx");
    }
}

function uploadFiles() {
    filesToUpload.forEach(({ file, id }) => {
        const formData = new FormData();
        formData.append("file", file);

        fetch('./config/upload.php', {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            document.querySelector(`#${id} .status-text`).innerHTML = result;
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector(`#${id} .status-text`).innerHTML = `<span class="failure">No se pudo cargar el archivo</span>`;
        });
    });
}