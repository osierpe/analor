interface form_props {
  form_data: Form_Data
  set_form_data: React.Dispatch<React.SetStateAction<Form_Data>>
}

class Form_Data {
  elementos: {
    nome: string
    tem: boolean | null
    quantidade: number | null
  }[] = []
  propriedades: {
    nome: string
    alcance: [number | null, number | null]
  }[] = []
  ecgf: {
    gFunc: null | string
    inex: 'incluir' | 'incSim' | 'excluir'
  }[] = []
  cas: string | null = null
  nome: string | null = null

  constructor() {
    this.elementos = [
      {
        nome: 'Carbono',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Hidrogênio',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Nitrogênio',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Oxigênio',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Flúor',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Cloro',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Bromo',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Iodo',
        tem: null,
        quantidade: null,
      },
      {
        nome: 'Enxofre',
        tem: null,
        quantidade: null,
      },
    ]

    this.propriedades = [
      {
        nome: 'Peso Molecular',
        alcance: [null, null],
      },
      {
        nome: 'Ponto de Fusão',
        alcance: [null, null],
      },
      {
        nome: 'Ponto de Ebulição',
        alcance: [null, null],
      },
    ]

    this.ecgf = [
      {
        gFunc: null,
        inex: 'incSim',
      },
      {
        gFunc: null,
        inex: 'incSim',
      },
      {
        gFunc: null,
        inex: 'incSim',
      },
      {
        gFunc: null,
        inex: 'incSim',
      },
      {
        gFunc: null,
        inex: 'incSim',
      },
      {
        gFunc: null,
        inex: 'incSim',
      },
    ]

    this.cas = null
    this.nome = null
  }
}

export { Form_Data }
export type { form_props }
