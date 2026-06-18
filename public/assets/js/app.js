document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.flash').forEach((el) => setTimeout(() => el.remove(), 4000));
  const sidebar = document.getElementById('sidebar');
  const sidebarIcon = document.querySelector('[data-sidebar-icon]');
  document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => btn.addEventListener('click', (e) => {
    e.stopPropagation(); sidebar?.classList.toggle('open');
    if (sidebarIcon) sidebarIcon.textContent = sidebar?.classList.contains('open') ? '\u2715' : '\u2630';
  }));
  document.querySelector('.main-area')?.addEventListener('click', (e) => {
    if (!e.target.closest('[data-sidebar-toggle]')) {
      sidebar?.classList.remove('open');
      if (sidebarIcon) sidebarIcon.textContent = '\u2630';
    }
  });
  sidebar?.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', () => sidebar.classList.remove('open')));
  document.querySelectorAll('form[data-confirm]').forEach((form) => form.addEventListener('submit', (event) => { if (!confirm(form.dataset.confirm || 'Continue?')) event.preventDefault(); }));
  const panels = document.querySelectorAll('[data-tab-panel]');
  if (panels.length) {
    panels[0].classList.add('active');
    document.querySelectorAll('[data-tab]').forEach((button) => button.addEventListener('click', () => {
      panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.tabPanel === button.dataset.tab));
    }));
  }
  const typeChart = document.getElementById('typeChart');
  if (typeChart && window.Chart) {
    new Chart(typeChart, {type:'pie',data:{labels:JSON.parse(typeChart.dataset.labels||'[]'),datasets:[{data:JSON.parse(typeChart.dataset.values||'[]'),backgroundColor:['#1A3C5E','#2D6A9F','#1A7F5E','#C97A2B','#B83232']}]}});
  }
  const lineChart = document.getElementById('lineChart');
  if (lineChart && window.Chart) {
    new Chart(lineChart, {type:'line',data:{labels:['Jan','Feb','Mar','Apr','May','Jun'],datasets:[{label:'Registrations',data:[0,0,0,0,0,0],borderColor:'#2D6A9F',tension:.3}]}});
  }
});
