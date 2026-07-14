import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormArray, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { RepuestosService } from '../../servicios/repuestos';

@Component({
  selector: 'app-formula-crear',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './formula-crear.html',
  styleUrls: ['./formula-crear.css']
})
export class FormulaCrearComponent implements OnInit {
  formulaForm!: FormGroup;
  repuestos: any[] = [];
  errorMessage: string = '';

  constructor(
    private fb: FormBuilder,
    private repuestoService: RepuestosService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.cargarRepuestos();
  }

  // Inicializa el formulario reactivo
  initForm(): void {
    this.formulaForm = this.fb.group({
      id_repuesto_padre: ['', Validators.required],
      insumos: this.fb.array([this.crearInsumoGroup()]) // Inicia con una fila vacía
    });
  }

  // Helper para acceder fácilmente al FormArray en el HTML
  get insumosFormArray(): FormArray {
    return this.formulaForm.get('insumos') as FormArray;
  }

  // Estructura de cada fila de insumo (Hijo + Cantidad)
  crearInsumoGroup(): FormGroup {
    return this.fb.group({
      id_repuesto_hijo: ['', Validators.required],
      cantidad: [1, [Validators.required, Validators.min(1)]]
    });
  }

  // Carga los repuestos desde PHP
  cargarRepuestos(): void {
    this.repuestoService.getRepuestos().subscribe({
      next: (data) => this.repuestos = data,
      error: () => this.errorMessage = 'No se pudo conectar con el servidor de PHP. Verifica que XAMPP esté encendido.'
    });
  }

  // Añade una nueva fila de insumo
  agregarInsumo(): void {
    this.insumosFormArray.push(this.crearInsumoGroup());
  }

  // Remueve una fila de insumo
  removerInsumo(index: number): void {
    if (this.insumosFormArray.length > 1) {
      this.insumosFormArray.removeAt(index);
    } else {
      alert("La receta debe tener por lo menos un componente básico.");
    }
  }

  // Envía el formulario final en formato JSON a PHP
  guardarReceta(): void {
    if (this.formulaForm.invalid) return;

    this.repuestoService.guardarFormula(this.formulaForm.value).subscribe({
      next: (res) => {
        alert('Fórmula guardada exitosamente, bro!');
        // Aquí puedes redirigir a la vista de fórmulas creadas
        // this.router.navigate(['/ruta-de-formulas']); 
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Error al guardar la fórmula.';
      }
    });
  }
}