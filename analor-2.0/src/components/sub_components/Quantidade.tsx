import { form_props } from '../../form'

interface quantidade_props extends form_props {
  elemento: {
    nome: string
    tem: boolean | null
    quantidade: number | null
  }
}

export default function Quantidade({
  form_data,
  set_form_data,
  elemento,
}: quantidade_props) {
  const handle_increment = function (isInverted: boolean = false) {
    const new_elementos_array: any[] = form_data.elementos.map((form_el) => {
      if (elemento.nome === form_el.nome) {
        if (form_el.quantidade !== null) {
          return {
            ...form_el,
            quantidade: isInverted
              ? form_el.quantidade > 1
                ? form_el.quantidade - 1
                : null
              : form_el.quantidade + 1,
          }
        } else {
          return {
            ...form_el,
            quantidade: 1,
          }
        }
      }
      return form_el
    })

    set_form_data({
      ...form_data,
      elementos: new_elementos_array,
    })
  }

  const handle_input = function (event: any) {
    const new_elementos_array = form_data.elementos.map((el) => {
      if (el.nome !== event.target.name) {
        return el
      }

      return {
        ...el,
        quantidade:
          Number(event.target.value) > 0 ? Number(event.target.value) : null,
      }
    })

    set_form_data((prev_form_data) => ({
      ...prev_form_data,
      elementos: new_elementos_array,
    }))
  }

  return (
    <div className={`quantidade ${!elemento.tem ? 'inactive' : ''}`}>
      <h3>Quantidade:</h3>
      <span className="btn" onClick={() => handle_increment()}>
        +
      </span>
      <span className="btn" onClick={() => handle_increment(true)}>
        -
      </span>
      <input
        type="number"
        min={1}
        name={elemento.nome}
        value={elemento.quantidade === null ? '' : elemento.quantidade}
        onChange={handle_input}
      />
      <span
        className={`btn question-mark ${
          elemento.quantidade === null ? 'active' : ''
        }`}
        onClick={() => true}
      >
        ?
      </span>
    </div>
  )
}
