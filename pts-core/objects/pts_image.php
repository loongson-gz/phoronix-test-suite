<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2010, Phoronix Media
	Copyright (C) 2010, Michael Larabel

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class pts_image
{
	public static function imagecreatefromtga($tga_file)
	{
		// There is no mainline PHP GD support for reading TGA at this time
		$handle = fopen($tga_file, "rb");
		$data = fread($handle, filesize($tga_file));
		fclose($handle);
	   
		$pointer = 18;
		$pixel_x = 0;
		$pixel_y = 0;
		$img_width = base_convert(bin2hex(strrev(substr($data, 12, 2))), 16, 10);
		$img_height = base_convert(bin2hex(strrev(substr($data, 14, 2))), 16, 10);
		$pixel_depth = base_convert(bin2hex(strrev(substr($data, 16, 1))), 16, 10);
		$bytes_per_pixel = $pixel_depth / 8;
		$img = imagecreatetruecolor($img_width, $img_height);

		while($pointer < strlen($data))
		{
			// right now it's only reading 3 bytes per pixel, even for ETQW and others have a pixel_depth of 32-bit, rather than replacing 3 with $bytes_per_pixel
			// reading 32-bit TGAs from Enemy Territory: Quake Wars seems to actually work this way even though it's 32-bit
			// 24-bit should be good in all cases
			imagesetpixel($img, $pixel_x, ($img_height - $pixel_y), base_convert(bin2hex(strrev(substr($data, $pointer, 3))), 16, 10));
			$pixel_x++;

			if($pixel_x == $img_width)
			{
				$pixel_y++;
				$pixel_x = 0;
			}

			$pointer += $bytes_per_pixel;
		}
	   
		return $img;
	}
	public static function image_file_to_gd($img_file)
	{
		$img = false;

		switch(strtolower(pts_last_element_in_array(explode('.', $img_file))))
		{
			case "tga":
				$img = pts_image::imagecreatefromtga($img_file);
				break;
			case "png":
				$img = imagecreatefrompng($img_file);
				break;
			case "jpg":
			case "jpeg":
				$img = imagecreatefromjpeg($img_file);
				break;
		}

		return $img;
	}
}

?>
