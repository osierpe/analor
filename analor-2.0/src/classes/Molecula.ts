export default class Molecula {
    cas: string;
    niup: string;
    ncom: string;
    ningles: string;
    nlin: string;
    flin: string;
    pmol: number;
    fmol: string;
    carb: number;
    hidr: number;
    oxig: number;
    nitr: number;
    enxo: number;
    clor: number;
    brom: number;
    iodo: number;
    fluo: number;
    pf: number;
    pfcarac: number;
    pe: number;
    pecarac: number;
    pfder: string;
    iv: number[];
    ivcar: number[];
    ms: number[];
    mscar: number[];
    ims: number[];
    imsar: number[];
    smiles: string;
    ecgf: string;
    estrutura: string;
    no_ifrj: boolean;

    constructor(
        cas: string,
        niup: string,
        ncom: string,
        ningles: string,
        nlin: string,
        flin: string,
        pmol: number,
        fmol: string,
        carb: number,
        hidr: number,
        oxig: number,
        nitr: number,
        enxo: number,
        clor: number,
        brom: number,
        iodo: number,
        fluo: number,
        pf: number,
        pfcarac: number,
        pe: number,
        pecarac: number,
        pfder: string,
        iv: number[],
        ivcar: number[],
        ms: number[],
        mscar: number[],
        ims: number[],
        imsar: number[],
        smiles: string,
        ecgf: string,
        estrutura: string,
        no_ifrj: boolean
    ) {
        this.cas = cas;
        this.niup = niup;
        this.ncom = ncom;
        this.ningles = ningles;
        this.nlin = nlin;
        this.flin = flin;
        this.pmol = pmol;
        this.fmol = fmol;
        this.carb = carb;
        this.hidr = hidr;
        this.oxig = oxig;
        this.nitr = nitr;
        this.enxo = enxo;
        this.clor = clor;
        this.brom = brom;
        this.iodo = iodo;
        this.fluo = fluo;
        this.pf = pf;
        this.pfcarac = pfcarac;
        this.pe = pe;
        this.pecarac = pecarac;
        this.pfder = pfder;
        this.iv = iv;
        this.ivcar = ivcar;
        this.ms = ms;
        this.mscar = mscar;
        this.ims = ims;
        this.imsar = imsar;
        this.smiles = smiles;
        this.ecgf = ecgf;
        this.estrutura = estrutura;
        this.no_ifrj = no_ifrj;
    }
}