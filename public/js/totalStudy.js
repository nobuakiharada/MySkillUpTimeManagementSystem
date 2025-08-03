// ページ全体の読み込み完了（画像など含む）を待ってアラート表示
window.onload = () => {
  const loadTimeInMs = performance.now();
  const loadTimeInSec = (loadTimeInMs / 1000).toFixed(3); // 秒に変換して小数第2位まで表示
  alert(`ページの読み込み完了までに ${loadTimeInSec} 秒かかりました`);
};

document.addEventListener('DOMContentLoaded', async () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const selectedMonth = document.getElementById('month')?.value;
    const deleteForms = document.querySelectorAll('.delete-form');
    
    // viewのDOMが完全に読み込まれた後に実行する自動未登録日補完処理
    try {
        const response = await fetch('/api/skillUpResult/fillCheck', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                selectedMonth: selectedMonth
            })
        });
        const data = await response.json();

        if (data.status === 'unfilled') {
            const confirmResult = confirm('未学習日があります。未登録日を登録しますか？');
            if (confirmResult) {
                if (selectedMonth) {
                    // 未登録日登録処理にリダイレクト
                    try {
                        // 未登録日登録処理にリダイレクト
                        window.location.href = `/skillUpResult/unique/unstudySave?month=${selectedMonth}`;
                    } catch (e) {
                        alert('未登録日登録処理に失敗しました。');
                        console.error('未登録日登録処理のリダイレクト中にエラー:', e);
                    }
                } else {
                    alert('対象月の取得に失敗しました。');
                }
            }
        } else if (data.status === 'already_filled') {
            alert('未登録日はありません');
        } else {
            alert('予期しない応答を受け取りました。');
        }
    } catch (error) {
        alert('未登録日補完処理に失敗しました。ネットワークまたはサーバーエラーです。');
        console.error('Error:', error);
    }

  deleteForms.forEach(form => {
    form.addEventListener('submit', (event) => {
      const confirmed = confirm('本当に総学習時間をリセットしてもよろしいですか？');
      if (!confirmed) {
        event.preventDefault(); // ← 送信を止める
      }
    });
  });
});