import { form_props } from '../form'

interface ecgf_props extends form_props {
  cur_displaying: number
  set_cur_displaying: React.Dispatch<React.SetStateAction<number>>
}

export default function Grupo_Funcional({
  form_data,
  set_form_data,
  cur_displaying,
  set_cur_displaying,
}: ecgf_props) {
  const handle_change = function (event: any) {
    const e_name = event.target.name
    const index = e_name.slice(-1)

    if (event.target.type === 'radio') {
      const new_arr = form_data.ecgf.map((ecgf, i) => {
        if (Number(index) !== i) {
          return ecgf
        }
        return {
          ...ecgf,
          inex: event.target.value,
        }
      })
      set_form_data((prev_form_data) => ({
        ...prev_form_data,
        ecgf: new_arr,
      }))
    } else if (event.target.type === 'text') {
      const new_arr = form_data.ecgf.map((ecgf, i) => {
        if (Number(index) !== i) {
          return ecgf
        }
        return {
          ...ecgf,
          gFunc: event.target.value !== '' ? event.target.value : null,
        }
      })
      set_form_data((prev_form_data) => ({
        ...prev_form_data,
        ecgf: new_arr,
      }))
    }
  }

  const ecgf_elements = form_data.ecgf.map((ecgf, i) => {
    return (
      <div className="ecgf" key={`ecgf${i}`}>
        <div className="dropdown">
          <img
            src="/public/dropdown_arrow.svg"
            alt="seta de dropdown"
            className="dropdown__arrow"
          />
          <input
            type="text"
            name={`gfunc${i}`}
            className="dropdown__value"
            value={ecgf.gFunc !== null ? ecgf.gFunc : ''}
            placeholder={`Grupo Funcional 0${i + 1}`}
            onChange={handle_change}
          />
          <div className="ecgf__buttons">
            <label>
              <input
                type="radio"
                value="incSim"
                name={`gfunc${i}`}
                onChange={handle_change}
                checked={ecgf.inex === 'incSim'}
              />
              Incluir Simultâneo
            </label>

            <label>
              <input
                type="radio"
                value="incluir"
                name={`gfunc${i}`}
                onChange={handle_change}
                checked={ecgf.inex === 'incluir'}
              />
              Incluir
            </label>

            <label>
              <input
                type="radio"
                value="excluir"
                name={`gfunc${i}`}
                onChange={handle_change}
                checked={ecgf.inex === 'excluir'}
              />
              Excluir
            </label>
          </div>
        </div>
      </div>
    )
  })

  function display_correct_amount(arr: Array<JSX.Element>, num: number) {
    const truncated_arr = []
    for (let i = 0; i < num; i++) {
      truncated_arr.push(arr[i])
    }
    return truncated_arr
  }

  return (
    <div className="grupo_funcional">
      <div className="grupo_funcional__header">
        <h2>Quais são os grupos funcionais presentes na amostra?</h2>
        <img src="/public/i-icon.svg" alt="ícone de informação" />
      </div>
      <div className="grupo_funcional__body">
        {display_correct_amount(ecgf_elements, cur_displaying)}
      </div>
      <div className="plus_button">
        <div
          className="plus_button__btn"
          onClick={() =>
            cur_displaying >= 6 ? null : set_cur_displaying(cur_displaying + 1)
          }
        >
          +
        </div>
      </div>
    </div>
  )
}
