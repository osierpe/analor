import React, { useState, useEffect } from 'react'

interface NavigationProps {
  set_cur_page: React.Dispatch<React.SetStateAction<number>>
  cur_page: number
}

export default function Navigation({
  set_cur_page,
  cur_page,
}: NavigationProps) {
  const [isMobile, setIsMobile] = useState(window.innerWidth <= 600)

  const handleResize = () => {
    setIsMobile(window.innerWidth <= 600)
  }

  useEffect(() => {
    window.addEventListener('resize', handleResize)
    return () => {
      window.removeEventListener('resize', handleResize)
    }
  }, [])

  const navigation_names: Array<string> = [
    'Elementos',
    'Propriedades',
    'Grupo_Funcional',
    'Identificadores',
  ]

  const formatar_underlines = (str: string): string => {
    if (!str.includes('_')) {
      return str
    }
    return str.split('_').join(' ')
  }

  const desktopNavigation = !isMobile && (
    <>
      {navigation_names.map((nome, i) => (
        <React.Fragment key={nome}>
          <div
            className={`nav_link ${cur_page === i ? 'active' : ''}`}
            onClick={() => set_cur_page(i)}
          >
            <h2>{formatar_underlines(nome)}</h2>
          </div>

          {i === navigation_names.length - 1 ? (
            ''
          ) : (
            <div className="separator"></div>
          )}
        </React.Fragment>
      ))}
    </>
  )

  const mobileNavigation = isMobile ? (
    <>
      {navigation_names.map((nome, i) => {
        if (i === cur_page) {
          return (
            <h2 key={nome} className="nav_link">
              {formatar_underlines(nome)}
            </h2>
          )
        }
      })}
    </>
  ) : null

  return <nav>{isMobile ? mobileNavigation : desktopNavigation}</nav>
}
