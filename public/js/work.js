function sendAction(action) {
  const status = document.getElementById('status');
  fetch(`/api/study/${action}`, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({})
  })
  .then(response => response.json())
  .then(data => {
      status.textContent = data.message;
  })
  .catch(error => {
      status.textContent = 'エラーが発生しました';
      console.error(error);
  });
}