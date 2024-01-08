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

  const check_min_max = (arr: Array<any>, name: string): Array<any> => {
    const new_arr = arr.map((prop) => {
      if (name !== prop.nome) {
        return prop
      }

      let [min, max] = prop.alcance

      if (min !== null && max !== null) {
        if (min > max) {
          max = min
        } else if (max < min) {
          min = max
        }
      }

      return {
        ...prop,
        alcance: [min, max],
      }
    })

    return new_arr
  }

  const handle_increment = (is_inverted = false, is_max = false) => {
    const can_go_below_zero = !(nome === 'Peso Molecular')

    const new_propriedades_array = form_data.propriedades.map((formProp) => {
      if (nome === formProp.nome && formProp.alcance !== null) {
        let [min, max] = formProp.alcance
        const updatedMin = is_max
          ? min
          : min === null
          ? 1
          : is_inverted
          ? can_go_below_zero
            ? --min
            : Math.max(0, --min)
          : ++min
        const updated_max = is_max
          ? max === null
            ? 1
            : is_inverted
            ? can_go_below_zero
              ? --max
              : Math.max(0, --max)
            : ++max
          : max

        return { ...formProp, alcance: [updatedMin, updated_max] }
      }
      return formProp
    })

    const updatedArray = check_min_max(new_propriedades_array, nome)
    set_form_data((prevFormData) => ({
      ...prevFormData,
      propriedades: updatedArray,
    }))
  }

  const handle_input = (event: any) => {
    const newPropsArray = form_data.propriedades.map((formProp) => {
      if (event.target.name.slice(0, -4) === formProp.nome) {
        const isMax = event.target.name.slice(-3) === 'max'
        const updated_alcance = isMax
          ? [formProp.alcance[0], Number(event.target.value)]
          : [Number(event.target.value), formProp.alcance[1]]

        return { ...formProp, alcance: updated_alcance }
      }
      return formProp
    })

    const updatedArray = check_min_max(
      newPropsArray,
      event.target.name.slice(0, -4),
    )
    set_form_data((prevFormData) => ({
      ...prevFormData,
      propriedades: updatedArray.map((prop) => ({
        ...prop,
        alcance: prop.alcance as [number | null, number | null],
      })),
    }))
  }

  const get_value = (val: string | undefined) => {
    const prop = form_data.propriedades.find(
      (formProp) => formProp.nome === nome,
    )

    if (prop) {
      const [min, max] = prop.alcance
      return val === 'min' && min !== null
        ? min
        : val === 'max' && max !== null
        ? max
        : undefined
    }

    return undefined
  }

  return (
    <div className="propriedades__body--range">
      <h3>{nome}:</h3>
      <div className="propriedades__body--range-min">
        <span className="propriedades__body--range-min_text">min</span>
        <div className="btn" onClick={() => handle_increment(false, false)}>
          +
        </div>
        <div className="btn" onClick={() => handle_increment(true, false)}>
          -
        </div>
        <input
          type="number"
          name={`${nome}-min`}
          value={get_value('min')}
          min={min !== -273 ? min : -273}
          onChange={handle_input}
        />
      </div>
      <div className="propriedades__body--range-max">
        <span className="propriedades__body--range-max_text">max</span>
        <div className="btn" onClick={() => handle_increment(false, true)}>
          +
        </div>
        <div className="btn" onClick={() => handle_increment(true, true)}>
          -
        </div>
        <input
          type="number"
          name={`${nome}-max`}
          value={get_value('max')}
          min={min !== -273 ? min : -273}
          onChange={handle_input}
        />
      </div>
    </div>
  )
}
