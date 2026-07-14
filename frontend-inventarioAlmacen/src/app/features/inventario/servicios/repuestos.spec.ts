import { TestBed } from '@angular/core/testing';

import { Repuestos } from './repuestos';

describe('Repuestos', () => {
  let service: Repuestos;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Repuestos);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
