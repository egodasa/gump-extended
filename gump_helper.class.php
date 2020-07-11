<?php
class GUMP_Extended extends GUMP
{
	private $helper_validation_rules = array();
	private $validation_filter = array();
	private $current_column = null;
	private $current_rule = null;
	private $is_error = true;
	private $validated_data;

	// Add New Validation
	public function column($kolom)
	{
		$this->resetCurrentColumn();
		$this->setCurrentColumn($kolom);
		if (!isset($this->helper_validation_rules[$kolom]))
		{
			$this->helper_validation_rules[$kolom] = array();
		}
		return $this;
	}

	// Add or Set new validation to column
	public function setValidation($rule_name, $message = null)
	{
		if (!isset($this->helper_validation_rules[$this->current_column]))
		{
			throw new Exception('Error : Kolom ' . $this->current_column . ' tidak ditemukan! Jalankan SELF->column($kolom) terlebih dahulu!');
		}
		else
		{
			$this->helper_validation_rules[$this->current_column][$rule_name] = $message;
		}
		return $this;
	}

	// add or set filter to column
	public function setFilter($filter)
	{
		if (!isset($this->helper_validation_rules[$this->current_column]))
		{
			throw new Exception('Error : Kolom ' . $this->current_column . ' tidak ditemukan! Jalankan SELF->column($kolom) terlebih dahulu!');
		}
		else
		{
			if (!isset($this->validation_filter[$this->current_column]))
			{
				$this->validation_filter[$this->current_column] = array();
			}
			else
			{
				if (in_array($filter, $this->validation_filter))
				{
					throw new Exception('Error : Filter "' . $filter . '" sudah ada pada kolom "' . $this->current_column . '"!');
				}
			}
			$this->validation_filter[$this->current_column][] = $filter;
		}
		return $this;
	}

	// remove filter from column
	public function removeFilter($filter)
	{
		if (!isset($this->helper_validation_rules[$this->current_column]))
		{
			throw new Exception('Error : Kolom ' . $this->current_column . ' tidak ditemukan! Jalankan SELF->column($kolom) terlebih dahulu!');
		}
		else
		{
			$index = array_search($filter, $this->validation_filter[$this->current_column]);
			if ($index !== false)
			{
				unset($this->validation_filter[$this->current_column][$index]);
			}
			else
			{
				throw new Exception('Error : Filter "' . $filter . '" tidak ditemukan pada kolom "' . $this->current_column . '"!');
			}
		}
	}

	// clear or remove all filter from column
	public function clearFilter()
	{
		unset($this->validation_filter);
		$this->validation_filter = array();
	}

	// remove validation from column
	// if no $rule_name, then remove all validation
	public function removeValidation($rule_name = null)
	{
		if (empty($this->helper_validation_rules[$this->current_column]))
		{
			throw new Exception('Error : Kolom ' . $this->current_column . ' tidak ditemukan! Jalankan SELF->column($kolom) terlebih dahulu!');
		}
		else
		{
			if ($rule_name == null)
			{
				// hapus semua rule pada kolom yg dipilih
				unset($this->helper_validation_rules[$this->current_column]);
			}
			else
			{
				if (empty($this->helper_validation_rules[$this->current_column][$rule_name]))
				{
					throw new Exception("Error : Kolom " . $this->current_column . " dengan rule " . $rule_name . " tidak ditemukan!");
				}
				else
				{
					unset($this->helper_validation_rules[$this->current_column][$rule_name]);
				}
			}
		}
		return $this;
	}

	// remove all validation from specific column
	public function clearValidation()
	{
		unset($this->helper_validation_rules);
		$this->helper_validation_rules = array();
	}

	// make a modification from single rule
	public function editValidationMessage($rule_name, $message)
	{
		if (empty($this->helper_validation_rules[$this->current_column]))
		{
			throw new Exception('Error : Kolom ' . $this->current_column . ' tidak ditemukan! Jalankan SELF->column($kolom) terlebih dahulu!');
		} else {
			if (empty($this->helper_validation_rules[$this->current_column][$rule_name]))
			{
				throw new Exception('Error : Kolom ' . $this->current_column . ' dengan rule ' . $rule_name . ' tidak ditemukan! Jalankan SELF->column($kolom)->setValidation($rule, $message = null) terlebih dahulu!');
			}
			else
			{
				$this->helper_validation_rules[$this->current_column][$rule_name] = $message;
			}
		}
		return $this;
	}

	// set current column to set new rule or filter
	private function setCurrentColumn($column)
	{
		$this->current_column = $column;
	}

	// reset current column
	private function resetCurrentColumn()
	{
		$this->current_column = null;
	}

	// create validation rule as array so we can use it for GUMP library
	public function generateValidationRules()
	{
		$result = array();
		foreach ($this->helper_validation_rules as $column => $data)
		{
			$rules = implode("|", array_keys($data));
			$result[$column] = $rules;
		}
		return $result;
	}

	// generate error message as array. So we can use it for GUMP library
	public function generateErrorMessages()
	{
		$result = $this->helper_validation_rules;
		foreach ($this->helper_validation_rules as $column => $data)
		{
			// jika isi kolom kosong alias tidak ada rule, maka hapus kolom tersebut
			if(empty($result[$column]))
			{
				unset($result[$column]);
			}
			else
			{
			
				// jika berisi, maka cek apakah isi pesan error pada rulenya ada atau tidak
				foreach ($data as $index => $message)
				{
					// nama rule dipecah menggunakan pemisah koma
					$rule_list = explode(",", $index);
					$rule_name = $rule_list[0];
					
					
					// jika isi pesan rule kosong, maka hapus isinya
					if (empty($message))
					{
						unset($result[$column][$index]);
					}
					else
					{
						// khusus rule yang bersifat ada pemisah koma, maka harus dihilangkan dulu komanya
						if(count($rule_list) > 1)
						{
							// buat rule baru dengan nama tanpa koma
							$result[$column][$rule_name] = $message;

							// hapus rule lama
							unset($result[$column][$index]);
						}
					}
					
				}
			}			
		}
		return $result;
	}

	// generate filter's array. So We can use it as parameter to GUMP library
	public function generateFilter()
	{
		$result = array();
		foreach ($this->validation_filter as $index => $data)
		{
			$result[$index] = implode("|", $data);
		}
		return $result;
	}

	// check data for validation
	public function checkData($data)
	{
		if (empty($this->helper_validation_rules))
		{
			throw new Exception('Error : Tidak ada rule validasi kosong!');
		}
		else
		{

			// set validation rules
			$this->validation_rules($this->GenerateValidationRules());

			$error_messages = $this->GenerateErrorMessages();

			if (!empty($error_messages))
			{
				// set field-rule specific error messages
				$this->set_fields_error_messages($error_messages);
			}

			if (!empty($this->validation_filter))
			{
				// set field-rule specific error messages
				$this->filter_rules($this->GenerateFilter());
			}
			$this->validated_data = $this->run($data);
			return $this;
		}
	}

	// check if last checked data is error or not
	public function isError()
	{
		if (empty($this))
		{
			throw new Exception('Error : Tidak ada data yang sedang divalidasi!');
		}
		else
		{
			return $this->errors();
		}
	}

	// get validate data after validation and filter process
	public function getValidatedData()
	{
		if ($this->isError())
		{
			throw new Exception('Error : Data tidak lolos validasi!');
		}
		else
		{
			return $this->validated_data;
		}
	}

	// get errors message as array
	public function getErrorsArray()
	{
		if ($this->isError())
		{
			return $this->get_errors_array();
		}
	}

	// get readable errors message as array, but formatted with HTML
	public function getReadableErrors()
	{
		if ($this->isError())
		{
			return $this->get_readable_errors();
		}
	}
}
