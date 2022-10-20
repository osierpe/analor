const dados = [
  {
    nome: 'Elementos',
    tipo: 'possui',
    elementos: [
      'Carbono',
      'Oxigênio',
      'Hidrogênio',
      'Nitrogênio',
      'Enxofre',
      'Cloro',
      'Bromo',
      'Iodo',
      'Flúor',
    ],
    descrição: `Marque a caixa "possui" dos elementos que tenha certeza de sua
          presença e/ou sua quantidade se souber. Caso não saiba se há ou não
          presença, deixe os campos deste elemento em branco. Caso tenha certeza
          de que não há presença, digite sua quantidade = 0, e deixe a caixa
          "possui" desmarcada.`,
  },
  {
    nome: 'Propriedades',
    tipo: 'alcance',
    propFisQui: ['Peso Molecular', 'Ponto de Fusão', 'Ponto de Ebulição'],
  },
  {
    nome: 'ecgf',
    tipo: 'incluir/excluir',
    descrição: [
      `Colocar na área de texto a abreviação referente ao grupo funcional (a
          referência a abreviação fica ao lado do botão "aplicar filtro")`,
      ` Incluir: seleciona as moléculas com os grupos assinalados (não
          necessariamente na mesma molécula) Incluir simultâneo: seleciona as
          moléculas com os grupos assinalados (na mesma molécula) Excluir:
          elimina as moléculas com os grupos assinalados.`,
    ],
  },
  {
    nome: 'CAS',
    tipo: 'texto',
  },
];

export default dados;
