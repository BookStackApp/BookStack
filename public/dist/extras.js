document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.querySelector('.toggle-view-btn');
  toggleBtn.addEventListener('click', function () {
    this.classList.toggle('on');
    document.querySelector('.tri-layout-container').classList.toggle('max');
  });
});
