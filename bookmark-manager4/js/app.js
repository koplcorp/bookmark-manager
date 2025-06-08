document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('add-form');
    const categoriesDiv = document.getElementById('categories');
    const searchInput = document.getElementById('search');

    function render(data) {
    categoriesDiv.innerHTML = '';
    data.forEach(b => {
        console.log(b);  // <- Zjisti strukturu objektu

        const div = document.createElement('div');
        div.style.padding = '10px';
        div.style.marginBottom = '8px';
        div.style.borderBottom = '1px solid #ccc';
        div.style.fontSize = '18px';
        div.style.display = 'flex';
        div.style.alignItems = 'center';
        div.style.justifyContent = 'space-between';

        const favicon = b.favicon ? b.favicon : 'assets/default-favicon.png';
        const img = document.createElement('img');
        img.src = favicon;
        img.width = 20;
        img.height = 20;
        img.style.marginRight = '10px';

        const link = document.createElement('a');
        link.href = b.url;
        link.target = '_blank';
        link.textContent = b.title;
        link.style.textDecoration = 'none';
        link.style.color = '#0066cc';

        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Smazat';
        // Zde použij správný klíč ID podle console.logu
        deleteBtn.setAttribute('data-id', b.id); 

        deleteBtn.addEventListener('click', () => {
            const id = deleteBtn.getAttribute('data-id');
            fetch('api/delete_bookmark.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            }).then(() => loadData());
        });

        const leftDiv = document.createElement('div');
        leftDiv.style.display = 'flex';
        leftDiv.style.alignItems = 'center';

        leftDiv.appendChild(img);
        leftDiv.appendChild(link);

        div.appendChild(leftDiv);
        div.appendChild(deleteBtn);

        categoriesDiv.appendChild(div);
    });
}

    function loadData(query = '') {
        fetch('search.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => render(data));
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        fetch('api/add_bookmark.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            form.reset();
            loadData(searchInput.value);
        });
    });

    searchInput.addEventListener('input', () => {
        loadData(searchInput.value);
    });

    // ⭐ Mazání záložek po kliknutí na tlačítko
    categoriesDiv.addEventListener('click', e => {
        if (e.target.tagName === 'BUTTON' && e.target.hasAttribute('data-id')) {
            const id = e.target.getAttribute('data-id');
            if (confirm('Opravdu chceš tuto záložku smazat?')) {
                fetch('api/delete_bookmark.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + encodeURIComponent(id)
                }).then(res => res.json())
                  .then(() => loadData(searchInput.value));
            }
        }
    });

    loadData();
});
