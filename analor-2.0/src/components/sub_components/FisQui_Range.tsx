import { Form_Data } from '../../form'
import React from 'react'

interface FisQui_Props {
  nome: string
  form_data: Form_Data
  set_form_data: React.Dispatch<React.SetStateAction<Form_Data>>
  min?: number
}

export default function FisQui_Range({
  nome,
  min,
  set_form_data,
  form_data,
}: FisQui_Props) {
  //TODO: Fazer um botão de clear

  function check_min_max(arr: Array<any>, name: string): Array<any> {
    const newArr = arr.map((prop) => {
      if (name !== prop.nome) {
        return prop
      }

      console.log(Math.min(prop.alcance[0], prop.alcance[1]))

      return {
        ...prop,
        alcance: [
          prop.alcance[0] === null
            ? prop.alcance[0]
            : prop.alcance[1] === null
            ? prop.alcance[0]
            : Math.min(prop.alcance[0], prop.alcance[1]),
          prop.alcance[1] === null
            ? prop.alcance[1]
            : Math.max(prop.alcance[0], prop.alcance[1]),
        ],
      }
    })

    return newArr
  }

  const handle_increment = function (
    isInverted: boolean = false,
    is_max: boolean = false,
  ) {
    let new_propriedades_array: any[] = form_data.propriedades.map(
      (form_prop) => {
        if (nome === form_prop.nome) {
          if (form_prop.alcance !== null) {
            return {
              ...form_prop,
              alcance: [
                is_max
                  ? form_prop.alcance[0]
                  : form_prop.alcance[0] === null
                  ? 1
                  : isInverted
                  ? --form_prop.alcance[0]
                  : ++form_prop.alcance[0],
                is_max
                  ? form_prop.alcance[1] === null
                    ? 1
                    : isInverted
                    ? --form_prop.alcance[1]
                    : ++form_prop.alcance[1]
                  : form_prop.alcance[1],
              ],
            }
          }
        }
        return form_prop
      },
    )

    new_propriedades_array = check_min_max(new_propriedades_array, nome)

    set_form_data((prev_form_data) => ({
      ...prev_form_data,
      propriedades: new_propriedades_array,
    }))
  }

  const handle_input = function (event: any) {
    let new_props_array = form_data.propriedades.map((form_prop) => {
      if (event.target.name.slice(0, -4) !== form_prop.nome) {
        return form_prop
      }

      const isMax = event.target.name.slice(-3) === 'max'

      return {
        ...form_prop,
        alcance: [
          isMax ? form_prop.alcance[0] : Number(event.target.value),
          isMax ? Number(event.target.value) : form_prop.alcance[1],
        ],
      }
    })

    new_props_array = check_min_max(
      new_props_array,
      event.target.name.slice(0, -4),
    )
    console.log(new_props_array, form_data.propriedades)

    set_form_data((prev_form_data) => ({
      ...prev_form_data,
      //Precisei fazer esse check extra de type só aqui, typescript é estranho as vezes. NÃO MEXER
      propriedades: new_props_array.map((prop) => ({
        ...prop,
        alcance: prop.alcance as [number | null, number | null],
      })),
    }))
  }

  function get_value(val: 'min' | 'max'): number | undefined {
    for (let i = 0; i < form_data.propriedades.length; i++) {
      if (form_data.propriedades[i].nome === nome) {
        if (val === 'min' && form_data.propriedades[i].alcance[0] !== null) {
          return form_data.propriedades[i].alcance[0] as number
        } else if (
          val === 'max' &&
          form_data.propriedades[i].alcance[1] !== null
        ) {
          return form_data.propriedades[i].alcance[1] as number
        }

        return undefined
      }
    }
  }

  return (
    <div className="propriedades__body--range">
      <h3>{nome}:</h3>
      <div className="min">
        <div className="plus" onClick={() => handle_increment(false, false)}>
          +
        </div>
        <div className="minus" onClick={() => handle_increment(true, false)}>
          -
        </div>
        <input
          type="number"
          name={`${nome}-min`}
          value={get_value('min')}
          min={min ? min : -273}
          onChange={handle_input}
        />
      </div>
      <div className="max">
        <div className="plus" onClick={() => handle_increment(false, true)}>
          +
        </div>
        <div className="minus" onClick={() => handle_increment(true, true)}>
          -
        </div>
        <input
          type="number"
          name={`${nome}-max`}
          value={get_value('max')}
          min={min ? min : -273}
          onChange={handle_input}
        />
      </div>
    </div>
  )
}
