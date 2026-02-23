import React, { useState } from 'react'
import { form_props } from '../form'
import { Abrev } from '../App'

interface ecgf_props extends form_props {
  cur_displaying: number
  is_mobile: boolean
  set_cur_displaying: React.Dispatch<React.SetStateAction<number>>
  abrevs: any
}

export default function Grupo_Funcional({
  form_data,
  set_form_data,
  cur_displaying,
  set_cur_displaying,
  abrevs,
  is_mobile: isMobile,
}: ecgf_props): JSX.Element {
  const [resolvedAbrevs, setResolvedAbrevs] = useState<Abrev[]>([])
  const [dropdownClicked, setDropdownClicked] = useState<boolean[]>(Array(6).fill(false))

  const handleChange = (event: React.ChangeEvent<HTMLInputElement>): void => {
    const { name, value, type } = event.target
    const index = Number(name.slice(-1))

    const newArr = form_data.ecgf.map((ecgf, i) => {
      if (i !== index) return ecgf
      return type === 'radio'
        ? { ...ecgf, inex: value }
        : { ...ecgf, gFunc: value !== '' ? value : null }
    })

    set_form_data((prev: any) => ({ ...prev, ecgf: newArr }))
  }

  const truncateElements = (elements: JSX.Element[], count: number): JSX.Element[] =>
    elements.slice(0, count)

  async function handleDropdown(index: number, _: React.MouseEvent<HTMLDivElement>): Promise<void> {
    setDropdownClicked(prev => {
      const newState = [...prev]
      newState[index] = !newState[index]
      return newState
    })

    if (resolvedAbrevs.length === 0) {
      try {

        const fetchedAbrevs: Abrev[] = await abrevs

        const grupoFuncionalAbrevs = fetchedAbrevs.filter(
          (item: Abrev) => item.pchave === 'GRUPO FUNCIONAL'
        )

        setResolvedAbrevs(grupoFuncionalAbrevs)
      } catch (error) {
        console.error('Error handling dropdown:', error)
      }
    }
  }

  function handleDropdownItemClick(index: number, value: string): void {
    const newArr = form_data.ecgf.map((ecgf, i) => {
      if (i !== index) return ecgf
      return { ...ecgf, gFunc: value }
    })
    set_form_data((prev: any) => ({ ...prev, ecgf: newArr }))
  }

  const ecgfElements = form_data.ecgf.map((ecgf, i) => {
    const btnContent = (
      <>
        <label>
          Incluir:
          <input
            type="radio"
            value="incluir"
            name={`gfunc${i}`}
            onChange={handleChange}
            checked={ecgf.inex === 'incluir'}
          />
        </label>
        <label>
          Incluir Simultâneo:
          <input
            type="radio"
            value="incSim"
            name={`gfunc${i}`}
            onChange={handleChange}
            checked={ecgf.inex === 'incSim'}
          />
        </label>
        <label>
          Excluir:
          <input
            type="radio"
            value="excluir"
            name={`gfunc${i}`}
            onChange={handleChange}
            checked={ecgf.inex === 'excluir'}
          />
        </label>
      </>
    )

    return (
      <React.Fragment key={`ecgf-wrapper${i}`}>
        <div className="ecgf">
          <div className="dropdown" onClick={(e) => handleDropdown(i, e)}>
            <img
              src="/dropdown_arrow.svg"
              alt="seta de dropdown"
              className="dropdown__arrow"
            />
            <div className="separator"></div>
            <input
              type="text"
              name={`gfunc${i}`}
              className="dropdown__value"
              value={ecgf.gFunc || ''}
              placeholder={`Grupo Funcional 0${i + 1}`}
              onChange={handleChange}
            />
            <div className="dropdown__suggestion hidden"></div>
          </div>
          {isMobile ? btnContent : <div className="ecgf__buttons">{btnContent}</div>}
        </div>
        <div className={`dropdown__container ${dropdownClicked[i] ? '' : 'hidden'}`}>
          {resolvedAbrevs.map((abrev) => (
            <p
              className="dropdown__item"
              key={abrev.nlin}
              onClick={() => handleDropdownItemClick(i, abrev.nlin)}
            >
              {abrev.nome}
            </p>
          ))}
        </div>
      </React.Fragment>
    )
  })

  return (
    <div className="grupo_funcional">
      <div className="grupo_funcional__header">
        <h2>Quais são os grupos funcionais presentes na amostra?</h2>
        <img src="/i-icon.svg" alt="ícone de informação" />
      </div>
      <div className="grupo_funcional__body">
        {truncateElements(ecgfElements, cur_displaying)}
      </div>
      {cur_displaying < 6 && (
        <div className="plus_button">
          <div
            className="plus_button__btn"
            onClick={() =>
              cur_displaying < 6 && set_cur_displaying(cur_displaying + 1)
            }
          >
            +
          </div>
        </div>
      )}
      <div className="bottom_separator"></div>
    </div>
  )
}
