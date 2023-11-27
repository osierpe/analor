interface NavigationProps {
    set_cur_page: React.Dispatch<React.SetStateAction<number>>;
  }
  
  export default function Navigation({ set_cur_page }: NavigationProps) {
    const navigation_names: Array<string> = ["Elementos", "Propriedades", "Grupo_Funcional", "Identificadores"];
  
    const formatar_underlines = (str: string): string => {
      if (!str.includes("_")) {
        return str;
      }
      return str.split("_").join(" ");
    };
  
    const navigation_elements = navigation_names.map((nome, i) => {
      return (
        <div className="nav_link" key={nome} onClick={() => set_cur_page(i)}>
          <h2>{formatar_underlines(nome)}</h2>
        </div>
      );
    });
  
    return <nav>{navigation_elements}</nav>;
}