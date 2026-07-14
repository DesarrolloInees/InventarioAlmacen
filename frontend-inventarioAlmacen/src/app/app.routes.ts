import { Routes } from '@angular/router';
import { LayoutComponent } from './shared/componentes/layout/layout.component';
import { FormulaCrearComponent } from './features/inventario/componentes/formula-crear/formula-crear';

export const routes: Routes = [
  {
    path: '',
    component: LayoutComponent, // El Layout es el marco principal
    children: [
      {
        path: 'formulas/crear',
        component: FormulaCrearComponent // Esta vista se pinta en el <router-outlet> de Layout
      },
      {
        path: '',
        redirectTo: 'formulas/crear',
        pathMatch: 'full'
      }
    ]
  }
];