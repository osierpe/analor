import GrupoFuncEsqCarForm from './GrupoFuncEsqCarForm';
const elements = [];
for (let i = 1; i < 7; i++) {
  elements.push(<GrupoFuncEsqCarForm num={i} key={`inex${i}`} />);
}

export default function GrupoFuncEsqCar() {
  return (
    <div className='gpFunc'>
      <h2>Grupo Funcional / Esqueleto de Carbono</h2>
      {elements}
      <div className='tooltip'>
        Modo de uso para busca por ECGF (Referencias)
        <span className='tooltiptext'>
          Colocar na área de texto a abreviação referente ao grupo funcional (a
          referência a abreviação fica ao lado do botão "aplicar filtro")
        </span>
      </div>
      <div className='tooltip'>
        Modo de uso para busca por ECGF (Botões)
        <span className='tooltiptext'>
          Incluir: seleciona as moléculas com os grupos assinalados (não
          necessariamente na mesma molécula) Incluir simultâneo: seleciona as
          moléculas com os grupos assinalados (na mesma molécula) Excluir:
          elimina as moléculas com os grupos assinalados.
        </span>
      </div>
    </div>
  );
}
