interface Submit_Props {
  handle_submit: () => void
}

export default function Submit_Btn({ handle_submit }: Submit_Props) {
  return (
    <button onClick={() => handle_submit()}>
      <img src="/symbol-1.svg" alt="símbolo de átomo" />
      Mostrar Resultado
    </button>
  )
}
