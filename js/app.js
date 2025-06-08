document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('add-form');
    const categoriesDiv = document.getElementById('categories');
    const searchInput = document.getElementById('search');

    let allData = [];

    function render(data) {
        categoriesDiv.innerHTML = '';
        const grouped = {};
        data.forEach(item => {
            if (!grouped[item.category_id]) {
                grouped[item.category_id] = {
                    name: item.name,
                    bookmarks: []
                };
            }
            if (item.bookmark_id) {
                grouped[item.category_id].bookmarks.push(item);
            }
        });

        for (let catId in grouped) {
            const box = document.createElement('div');
            box.className = 'category-box';
            box.innerHTML = `<h2>${grouped[catId].name}</h2><ul></ul>`;
            grouped[catId].bookmarks.forEach(b => {
                const li = document.createElement('li');
                li.innerHTML = `<img src="${b.favicon}" width="16" height="16"> <a href="${b.url}" target="_blank">${b.title}</a>`;
                box.querySelector('ul').appendChild(li);
            });
            categoriesDiv.appendChild(box);
        }
    }

    function loadData() {
        fetch('api/get_data.php')
            .then(res => res.json())
            .then(data => {
                allData = data;
                render(allData);
            });
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        fetch('api/add_bookmark.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            form.reset();
            loadData();
        });
    });

    searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim();

    if (query.length === 0) {
        // Když je vyhledávací pole prázdné, zobraz všechny záložky
        render(allData);
        return;
    }

    fetch(`search.php?search=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            render(data);
        })
        .catch(err => {
            console.error('Chyba pøi vyhledávání:', err);
        });
});

    loadData();
});
