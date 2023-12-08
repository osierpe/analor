import { form_props, Form_Data } from '../form'
import Quantidade from './sub_components/Quantidade'

interface Elementos_Props extends form_props {
  set_form_data: React.Dispatch<React.SetStateAction<Form_Data>>
}

export default function Elementos({
  form_data,
  set_form_data,
}: Elementos_Props) {
  const handle_el_btn = function (nome: string, val: boolean | null): void {
    const new_elementos_array = form_data.elementos.map((el) => {
      if (el.nome !== nome) return el
      return {
        nome: el.nome,
        quantidade: el.quantidade,
        tem: val,
      }
    })
    set_form_data((prev_form_data) => ({
      ...prev_form_data,
      elementos: new_elementos_array,
    }))
  }

  const elementos_elements = form_data.elementos.map((elemento) => (
    <div className="elementos__body--elemento" key={elemento.nome}>
      <h3>Possui {elemento.nome}?</h3>

      <button
        className={`sim ${elemento.tem ? 'active' : ''}`}
        onClick={() => handle_el_btn(elemento.nome, true)}
      >
        Sim
      </button>
      <button
        className={`nao ${elemento.tem === false ? 'active' : ''}`}
        onClick={() => handle_el_btn(elemento.nome, false)}
      >
        Não
      </button>
      <button
        className={`nao-sei ${elemento.tem === null ? 'active' : ''}`}
        onClick={() => handle_el_btn(elemento.nome, null)}
      >
        Não sei
      </button>

      {elemento.tem === true ? (
        <Quantidade
          form_data={form_data}
          elemento={elemento}
          set_form_data={set_form_data}
        />
      ) : null}
    </div>
  ))

  return (
    <div className="elementos">
      <div className="elementos__header">
        <h2>Quais elementos estão presentes na amostra?</h2>
        <img
          src="/i-icon.svg"
          alt="ícone de informação"
          className="elementos__header--info"
        />
      </div>
      <div className="elementos__body">{elementos_elements}</div>
    </div>
  )
}
