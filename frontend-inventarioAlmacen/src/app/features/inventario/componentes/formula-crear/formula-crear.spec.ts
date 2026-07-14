import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormulaCrear } from './formula-crear';

describe('FormulaCrear', () => {
  let component: FormulaCrear;
  let fixture: ComponentFixture<FormulaCrear>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormulaCrear],
    }).compileComponents();

    fixture = TestBed.createComponent(FormulaCrear);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
