const pegarIniciais = function (str) {
  const palavras = str.split(' ');
  if (palavras.indexOf('de') > 0) {
    palavras.splice(palavras.indexOf('de'), 1);
  }
  return (palavras[0][0] + palavras[1][0]).toLowerCase();
};

const func = { pegarIniciais };

export default func
