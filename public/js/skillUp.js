// ナビゲーション開始時刻を保持（グローバル）
const navigationStart = performance.timeOrigin || performance.timing.navigationStart;

// ページ全体の読み込み完了（画像など含む）を待ってアラート表示
window.onload = () => {
  const loadTimeInMs = performance.now();
  const loadTimeInSec = (loadTimeInMs / 1000).toFixed(3); // 秒に変換して小数第2位まで表示
  alert(`ページの読み込み完了までに ${loadTimeInSec} 秒かかりました`);
};

window.addEventListener('DOMContentLoaded', () => {
  // 休憩ボタンすべて取得
  const breakButtons = document.querySelectorAll('form[action*="break"] button');

  breakButtons.forEach(button => {
    button.addEventListener('click', function (event) {
      const message = this.dataset.message || "休憩しますか？";
      if (!confirm(message)) {
        event.preventDefault(); // キャンセルで送信止める
      }
    });
  });
});