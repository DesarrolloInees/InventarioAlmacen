import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RepuestosService {
  // Ahora apuntamos a tu servidor web de XAMPP usando parámetros de consulta
  private API_URL = 'http://localhost/InventarioAlmacen/index.php?pagina=repuestoFormulaCrear'; 

  constructor(private http: HttpClient) {}

  getRepuestos(): Observable<any[]> {
    return this.http.get<any[]>(this.API_URL);
  }

  guardarFormula(formulaData: any): Observable<any> {
    return this.http.post<any>(this.API_URL, formulaData);
  }
}