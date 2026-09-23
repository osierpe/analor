import { useEffect, useRef } from 'react'
import SmilesDrawer from 'smiles-drawer'

interface EstruturaMolecula_Props {
  smiles: string
}

const drawer_options = {
  width: 220,
  height: 140,
  // Rótulos de átomos com texto concatenado (ex.: "O2N" do grupo nitro)
  // se estendem além da posição do átomo. Um padding pequeno deixa esse
  // texto ultrapassar o viewBox calculado e ser cortado pelo <svg>.
  padding: 25,
}

export default function EstruturaMolecula({ smiles }: EstruturaMolecula_Props) {
  const svg_ref = useRef<SVGSVGElement>(null)

  useEffect(() => {
    const svg_element = svg_ref.current
    if (!smiles || !svg_element) {
      return
    }

    let drawn = false
    const draw = () => {
      if (drawn) return
      drawn = true
      const svg_drawer = new SmilesDrawer.SvgDrawer(drawer_options)
      SmilesDrawer.parse(
        smiles,
        (tree) => svg_drawer.draw(tree, svg_element, 'light'),
        () => {
          // SMILES ausente/inválido: deixa o container vazio em vez de quebrar a tela
        },
      )
    }

    // O card do resultado começa colapsado (display:none, revelado via
    // toggle de classe CSS ao clicar "Expandir") — um elemento invisível
    // tem tamanho zero, então a lib de desenho mede tudo errado se rodar
    // nesse momento. Espera o container ganhar tamanho real antes de desenhar.
    const observer = new ResizeObserver((entries) => {
      const rect = entries[0]?.contentRect
      if (rect && (rect.width > 0 || rect.height > 0)) {
        draw()
        observer.disconnect()
      }
    })
    observer.observe(svg_element)

    return () => observer.disconnect()
  }, [smiles])

  if (!smiles) {
    return null
  }

  return <svg ref={svg_ref} className="estrutura_molecula" />
}
