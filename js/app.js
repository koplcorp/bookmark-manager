document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('add-form');
    const categoriesDiv = document.getElementById('categories');
    const searchInput = document.getElementById('search');
    const loadMoreBtn = document.getElementById('load-more');
    const bookmarkCount = document.getElementById('bookmark-count');

    let limit = 10;
    let offset = 0;
    let loadingMore = false;
    let allData = [];
    let currentQuery = '';

    // Přidána truncate funkce
    function truncate(str, n) {
        return (str.length > n) ? str.substr(0, n - 3) + '...' : str;
    }

    function render(data) {
        
        selectedIndex = -1;
        
        categoriesDiv.innerHTML = '';
        bookmarkCount.textContent = `Zobrazeno záložek: ${data.length}`;

        data.forEach(b => {
            const div = document.createElement('div');
            div.className = 'bookmark-item';
            div.style.cursor = 'pointer';
            div.addEventListener('click', () => {
                window.open(b.url, '_blank');
            });

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
            link.style.display = 'block';
            
            // Neotevre se 2x odkaz
            link.addEventListener('click', e => {
                e.stopPropagation();
            });

            // Upravený element pro URL s truncate a zalamováním
            const urlText = document.createElement('small');
            urlText.className = 'break-word';                   // přidána třída pro zalamování
            urlText.textContent = truncate(b.url, 70);          // ořez na 70 znaků
            urlText.title = b.url;                               // tooltip s celou URL
            urlText.style.display = 'block';
            urlText.style.marginTop = '2px';

            const leftDiv = document.createElement('div');
            leftDiv.style.display = 'flex';
            leftDiv.style.alignItems = 'center';
            leftDiv.appendChild(img);

            const textContainer = document.createElement('div');
            textContainer.appendChild(link);
            textContainer.appendChild(urlText);
            leftDiv.appendChild(textContainer);

            div.appendChild(leftDiv);

            if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
                const editBtn = document.createElement('button');
                editBtn.textContent = 'Upravit';
                editBtn.setAttribute('data-id', b.id);
                editBtn.style.marginLeft = '10px';

                editBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); // 🛑 Zabrání otevření odkazu
                    const newTitle = prompt('Zadej nový název:', b.title);
                    const newUrl = prompt('Zadej novou URL:', b.url);
                    if (newTitle && newUrl) {
                        fetch('api/edit_bookmark.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: b.id, title: newTitle, url: newUrl })
                        }).then(() => {
                            offset = 0;
                            allData = [];
                            loadData(currentQuery);
                        });
                    }
                });

                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Smazat';
                deleteBtn.setAttribute('data-id', b.id);
                deleteBtn.style.marginLeft = '10px';

                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); // 🛑 Zabrání otevření odkazu
                    const id = deleteBtn.getAttribute('data-id');
                    if (confirm('Opravdu chceš tuto záložku smazat?')) {
                        fetch('api/delete_bookmark.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id })
                        }).then(() => {
                            offset = 0;
                            allData = [];
                            loadData(currentQuery);
                        });
                    }
                });

                const actionsDiv = document.createElement('div');
                actionsDiv.style.display = 'flex';
                actionsDiv.style.gap = '8px';
                actionsDiv.appendChild(editBtn);
                actionsDiv.appendChild(deleteBtn);
                div.appendChild(actionsDiv);
            }

            categoriesDiv.appendChild(div);
        });
    }

    function loadData(query = '', append = false) {
        loadingMore = true;
        let url = `search.php?q=${encodeURIComponent(query)}`;
        if (query.trim() === '') {
            url += `&limit=${limit}&offset=${offset}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (append) {
                    allData = allData.concat(data);
                } else {
                    allData = data;
                }
                render(allData);
                loadingMore = false;

                if (query.trim() === '' && data.length === limit) {
                    loadMoreBtn.style.display = 'block';
                } else {
                    loadMoreBtn.style.display = 'none';
                }
            });
    }

    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(form);
            fetch('api/add_bookmark.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                form.reset();
                offset = 0;
                allData = [];
                loadData(currentQuery);
            });
        });
    }

    searchInput.addEventListener('input', () => {
        offset = 0;
        currentQuery = searchInput.value;
        allData = [];
        loadData(currentQuery, false);
    });

    loadMoreBtn.addEventListener('click', () => {
        if (!loadingMore) {
            offset += limit;
            loadData(currentQuery, true);
        }
    });

    loadData();








    let selectedIndex = -1;

    function updateActiveItem() {
        const items = document.querySelectorAll('.bookmark-item');
        items.forEach((el, idx) => {
            if (idx === selectedIndex) {
                el.classList.add('active');
                el.scrollIntoView({ block: 'nearest' });
            } else {
                el.classList.remove('active');
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        const items = document.querySelectorAll('.bookmark-item');
        if (items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(items.length - 1, selectedIndex + 1);
            updateActiveItem();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(0, selectedIndex - 1);
            updateActiveItem();
        } else if (e.key === 'Enter') {
            if (selectedIndex >= 0 && selectedIndex < items.length) {
                const url = items[selectedIndex].querySelector('a')?.href;
                if (url) {
                    window.open(url, '_blank');
                }
            }
        }
    });








});
