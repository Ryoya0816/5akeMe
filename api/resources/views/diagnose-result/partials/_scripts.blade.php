{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartLabels = @json($chartLabels);
    const chartValues = @json($chartValues);

    const ctx = document.getElementById('diagnose-chart');

    if (ctx) {
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'お酒タイプのバランス',
                    data: chartValues,

                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,

                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--brand-main').trim() || '#9c3f2e',
                    backgroundColor: 'rgba(156, 63, 46, 0.12)',
                    pointBackgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--brand-main').trim() || '#9c3f2e',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        suggestedMin: 0,
                        suggestedMax: 5,
                        ticks: {
                            stepSize: 1,
                            backdropColor: 'transparent',
                            font: { size: 11 },
                            color: '#a08070',
                            z: 1
                        },
                        grid: {
                            circular: true,
                            color: 'rgba(0,0,0,0.06)'
                        },
                        angleLines: {
                            color: 'rgba(0,0,0,0.08)'
                        },
                        pointLabels: {
                            font: { size: 14, weight: '600' },
                            color: '#5a4030',
                            padding: 18
                        }
                    }
                }
            }
        });
    }

    const btnShowStores = document.getElementById('btn-show-stores');
    if (btnShowStores) {
        btnShowStores.addEventListener('click', function () {
            const storesSection = document.getElementById('stores-section');
            if (storesSection) {
                storesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // =========================================
    // フィードバック機能
    // =========================================
    const resultId = @json($result->result_id ?? null);
    const feedbackStars = document.querySelectorAll('.dr-star');
    const feedbackSelected = document.getElementById('feedback-selected');
    const commentWrap = document.getElementById('comment-wrap');
    const feedbackComment = document.getElementById('feedback-comment');
    const feedbackSubmit = document.getElementById('feedback-submit');
    const feedbackForm = document.getElementById('feedback-form');
    const feedbackDone = document.getElementById('feedback-done');

    const ratingLabels = {
        1: 'イマイチ 😕',
        2: 'まあまあ 🤔',
        3: '普通 😐',
        4: '良い 😊',
        5: '最高！ 🎉'
    };

    let selectedRating = 0;

    if (resultId) {
        fetch(`/api/diagnose/feedback/${resultId}/check`)
            .then(res => res.json())
            .then(data => {
                if (data.has_feedback) {
                    feedbackForm.style.display = 'none';
                    feedbackDone.style.display = 'block';
                    feedbackDone.querySelector('.dr-feedback-done-text').textContent = 
                        `評価済み: ${ratingLabels[data.rating] || ''}`;
                }
            })
            .catch(() => {});
    }

    feedbackStars.forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.rating);
            
            feedbackStars.forEach((s, index) => {
                if (index < selectedRating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });

            feedbackSelected.textContent = ratingLabels[selectedRating] || '';

            commentWrap.style.display = 'block';
        });

        star.addEventListener('mouseenter', function() {
            const hoverRating = parseInt(this.dataset.rating);
            feedbackStars.forEach((s, index) => {
                if (index < hoverRating) {
                    s.style.filter = 'grayscale(0%)';
                    s.style.opacity = '0.8';
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            feedbackStars.forEach((s, index) => {
                if (!s.classList.contains('active')) {
                    s.style.filter = 'grayscale(100%)';
                    s.style.opacity = '0.4';
                } else {
                    s.style.filter = 'grayscale(0%)';
                    s.style.opacity = '1';
                }
            });
        });
    });

    if (feedbackSubmit) {
        feedbackSubmit.addEventListener('click', async function() {
            if (!selectedRating || !resultId) return;

            this.disabled = true;
            this.textContent = '送信中...';

            try {
                const response = await fetch(`/api/diagnose/feedback/${resultId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({
                        rating: selectedRating,
                        comment: feedbackComment.value || null,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    feedbackForm.style.display = 'none';
                    feedbackDone.style.display = 'block';
                } else {
                    alert(data.message || 'エラーが発生しました');
                    this.disabled = false;
                    this.textContent = '送信する';
                }
            } catch (error) {
                alert('通信エラーが発生しました');
                this.disabled = false;
                this.textContent = '送信する';
            }
        });
    }

    // =========================================
    // URLコピー機能
    // =========================================
    const copyUrlBtn = document.getElementById('copy-url-btn');
    const copyBtnText = document.getElementById('copy-btn-text');

    if (copyUrlBtn) {
        copyUrlBtn.addEventListener('click', async function() {
            const url = this.dataset.url;
            
            try {
                await navigator.clipboard.writeText(url);
                
                copyUrlBtn.classList.add('copied');
                copyBtnText.textContent = 'コピーしました！';
                
                setTimeout(() => {
                    copyUrlBtn.classList.remove('copied');
                    copyBtnText.textContent = 'URLをコピー';
                }, 2000);
            } catch (err) {
                const textarea = document.createElement('textarea');
                textarea.value = url;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                
                try {
                    document.execCommand('copy');
                    copyUrlBtn.classList.add('copied');
                    copyBtnText.textContent = 'コピーしました！';
                    
                    setTimeout(() => {
                        copyUrlBtn.classList.remove('copied');
                        copyBtnText.textContent = 'URLをコピー';
                    }, 2000);
                } catch (e) {
                    alert('コピーに失敗しました。URLを手動でコピーしてください。');
                }
                
                document.body.removeChild(textarea);
            }
        });
    }
</script>
