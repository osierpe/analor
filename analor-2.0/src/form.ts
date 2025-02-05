interface form_props {
  form_data: Form_Data
  set_form_data: React.Dispatch<React.SetStateAction<Form_Data>>
}

class Form_Data {
  elementos: {
    nome: string
    tem: boolean | null
    quantidade: number | String
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
        quantidade: '',
      },
      {
        nome: 'Hidrogênio',
        tem: null,
        quantidade: '',
      },
      {
        nome: 'Nitrogênio',
        tem: null,
        quantidade: '',
      },
      {
        nome: 'Oxigênio',
        tem: null,
        quantidade: '',
      },
      {
        nome: 'Flúor',
        tem: null,
        quantidade: '',
      },
      {
        nome: 'Cloro',
        tem: null,
        quantidade: '',
      },
      {
        nome: 'Bromo',
        tem: null,
        quantidade: '',
      },
      {
        nome: 'Iodo',
        tem: null,
        quantidade: '',
      },
      {
        nome: 'Enxofre',
        tem: null,
        quantidade: '',
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
        inex: 'incluir',
      },
      {
        gFunc: null,
        inex: 'incluir',
      },
      {
        gFunc: null,
        inex: 'incluir',
      },
      {
        gFunc: null,
        inex: 'incluir',
      },
      {
        gFunc: null,
        inex: 'incluir',
      },
      {
        gFunc: null,
        inex: 'incluir',
      },
    ]

    this.cas = null
    this.nome = null
  }
}

export { Form_Data }
export type { form_props }
