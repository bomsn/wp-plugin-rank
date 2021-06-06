<h3><b><?php echo WPPR_Name; ?> Rankings</b></h3>
<table class="wppr-plugin-rank">
    <!-- Titles -->
    <thead>
        <tr>
            <th>Keyword</th>
            <th>Page</th>
            <th>Position</th>
            <th>Top 5 competitors</th>
            <th>Top competitor tags</th>
        </tr>
    </thead>

    <!-- Data -->
    <tbody>
        <?php foreach ($args as $key => $data) { ?>
            <tr class="<?php echo in_array($data['tag'], WPPR_Keywords) ? 'wppr-keyword-row' : 'wppr-tag-row'; ?>">
                <!-- Search tag -->
                <td class="wppr-search-tag"><?php echo $data['tag'] ?? $key; ?></td>
                <!-- Page -->
                <td class="wppr-page"><?php echo $data['page']; ?></td>
                <!-- Position -->
                <td class="wppr-position"><?php echo $data['position']; ?></td>
                <!-- Top competitors -->
                <td class="wppr-top-competitors">
                    <?php if (!empty($data['top_competitors'])) { ?>
                        <ul>
                            <?php foreach ($data['top_competitors'] as $competitor) { ?>
                                <li>
                                    <?php echo $competitor['name'] ?> <strong>(<?php echo $competitor['slug']; ?>)</strong><br>
                                    Downloads ( <strong><?php echo wppr_get_formatted_number($competitor['downloaded']); ?></strong> ),
                                    Installs ( <strong><?php echo wppr_get_formatted_number($competitor['active_installs']); ?></strong> ),
                                    Rating ( <strong><?php echo $competitor['rating']; ?>%</strong> )
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </td>
                <!-- Top tags -->
                <td class="wppr-top-competitor-tags">
                    <?php
                    if (!empty($data['top_competitor_tags'])) {
                        $i = 0;
                        $top_tags = "";
                        foreach ($data['top_competitor_tags'] as $tag) {
                            if ($i > 10) {
                                break;
                            }
                            $top_tags .= $tag['label'] . ', ';
                            $i++;
                        }

                        echo trim($top_tags, ',');
                    }
                    ?>
                </td>

            </tr>
        <?php } ?>
    </tbody>
</table>