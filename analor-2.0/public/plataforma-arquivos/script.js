// Subpáginas que continuam hospedadas no Google Sites.
const SITE_GOOGLE = 'https://sites.google.com/view/analor/'

document.querySelectorAll('.cartao[data-pagina]').forEach((cartao) => {
  cartao.href = SITE_GOOGLE + encodeURIComponent(cartao.dataset.pagina)
  cartao.target = '_blank'
  cartao.rel = 'noopener'
})

// Menu recolhível no celular.
const botao = document.querySelector('.menu-botao')
const menu = document.getElementById('menu')

function fecharMenu() {
  menu.classList.remove('aberto')
  botao.setAttribute('aria-expanded', 'false')
}

botao.addEventListener('click', () => {
  const aberto = menu.classList.toggle('aberto')
  botao.setAttribute('aria-expanded', String(aberto))
})

menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', fecharMenu))
