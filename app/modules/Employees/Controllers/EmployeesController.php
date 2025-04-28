<?php
namespace App\Modules\Employees\Controllers;

use Core\Controller;
use App\Modules\Employees\Models\Employee;
use Illuminate\Support\Str;

/**
 * Class EmployeesController
 *
 * Standard resource controller for Employees
 *
 * @package App\Modules\Employees\Controllers
 */
class EmployeesController extends Controller
{
    /**
     * Display a listing of the employees.
     *
     * @return void
     */
    public function index(): void
    {
        $employees = Employee::all();
        $this->view('Employees/Views/index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return void
     */
    public function create(): void
    {
        $this->view('Employees/Views/create');
    }

    /**
     * Store a newly created employee in storage.
     *
     * @return void
     */
    public function store(): void
    {
        // TODO: Add real validation/sanitization
        $data = [
            'id'         => Str::uuid()->toString(),
            'first_name' => $_POST['first_name'] ?? '',
            'last_name'  => $_POST['last_name']  ?? '',
            'email'      => $_POST['email']      ?? '',
            // add other fillable fields here
        ];

        Employee::create($data);
        $_SESSION['flash']['success'] = 'Employee created successfully.';
        $this->redirect('/employees');
    }

    /**
     * Display the specified employee.
     *
     * @param string $id
     * @return void
     */
    public function show(string $id): void
    {
        $employee = Employee::find($id);

        if (! $employee) {
            $_SESSION['flash']['error'] = 'Employee not found.';
            return $this->redirect('/employees');
        }

        $this->view('Employees/Views/show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param string $id
     * @return void
     */
    public function edit(string $id): void
    {
        $employee = Employee::find($id);

        if (! $employee) {
            $_SESSION['flash']['error'] = 'Employee not found.';
            return $this->redirect('/employees');
        }

        $this->view('Employees/Views/edit', compact('employee'));
    }

    /**
     * Update the specified employee in storage.
     *
     * @param string $id
     * @return void
     */
    public function update(string $id): void
    {
        // TODO: Add real validation/sanitization
        $employee = Employee::find($id);

        if (! $employee) {
            $_SESSION['flash']['error'] = 'Employee not found.';
            return $this->redirect('/employees');
        }

        $employee->first_name = $_POST['first_name'] ?? $employee->first_name;
        $employee->last_name  = $_POST['last_name']  ?? $employee->last_name;
        $employee->email      = $_POST['email']      ?? $employee->email;
        // assign other fields as needed

        $employee->save();

        $_SESSION['flash']['success'] = 'Employee updated successfully.';
        $this->redirect("/employees/{$id}");
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param string $id
     * @return void
     */
    public function destroy(string $id): void
    {
        $employee = Employee::find($id);

        if (! $employee) {
            $_SESSION['flash']['error'] = 'Employee not found.';
            return $this->redirect('/employees');
        }

        $employee->delete();
        $_SESSION['flash']['success'] = 'Employee deleted.';

        $this->redirect('/employees');
    }
}
